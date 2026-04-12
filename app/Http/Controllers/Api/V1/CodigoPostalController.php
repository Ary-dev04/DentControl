<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CodigoPostal;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SplFileObject;
use Throwable;

class CodigoPostalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $codigoPostal = $request->query('codigo_postal');

        if (! $codigoPostal) {
            return response()->json([
                'message' => 'Debes enviar el parametro codigo_postal.',
            ], 422);
        }

        return $this->show($codigoPostal);
    }

    public function show(string $codigoPostal): JsonResponse
    {
        if (! preg_match('/^\d{5}$/', $codigoPostal)) {
            return response()->json([
                'message' => 'El codigo postal debe tener exactamente 5 digitos.',
            ], 422);
        }

        $dbUnavailable = false;
        $hasCatalog = false;

        try {
            $results = CodigoPostal::query()
                ->where('codigo_postal', $codigoPostal)
                ->orderBy('asentamiento')
                ->get();

            if ($results->isNotEmpty()) {
                return $this->responseFromResults($results, 'catalogo_local');
            }

            $hasCatalog = CodigoPostal::query()->exists();
        } catch (Throwable $exception) {
            report($exception);
            $dbUnavailable = true;
        }

        $fileResponse = $this->searchInSepomexFile($codigoPostal);

        if ($fileResponse) {
            return $fileResponse;
        }

        $fallbackResponse = $this->searchInCopomex($codigoPostal);

        if ($fallbackResponse) {
            return $fallbackResponse;
        }

        if ($dbUnavailable) {
            return response()->json([
                'message' => 'No se pudo consultar el catalogo postal en la base de datos. Verifica la conexion MySQL o configura SEPOMEX_FILE_PATH como respaldo local.',
            ], 503);
        }

        if (! $hasCatalog) {
            return response()->json([
                'message' => 'El catalogo postal local aun no ha sido cargado. Importa el catalogo oficial de SEPOMEX o configura COPOMEX_TOKEN como respaldo temporal.',
            ], 503);
        }

        return response()->json([
            'message' => 'No se encontro informacion para el codigo postal solicitado.',
        ], 404);
    }

    protected function responseFromResults(Collection $results, string $source): JsonResponse
    {
        $first = $results->first();

        return response()->json([
            'data' => [
                'codigo_postal' => $first->codigo_postal,
                'estado' => $first->estado,
                'municipio' => $first->municipio,
                'ciudad' => $first->ciudad,
                'colonias' => $results
                    ->map(fn ($item) => [
                        'nombre' => $item->asentamiento,
                        'tipo_asentamiento' => $item->tipo_asentamiento,
                        'zona' => $item->zona,
                    ])
                    ->unique('nombre')
                    ->values(),
                'source' => $source,
            ],
        ]);
    }

    protected function searchInSepomexFile(string $codigoPostal): ?JsonResponse
    {
        $path = config('services.sepomex.file_path');

        if (! $path || ! is_file($path)) {
            return null;
        }

        $delimiter = config('services.sepomex.delimiter') ?: $this->detectDelimiter($path);
        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $file->setCsvControl($delimiter);

        $headerMap = null;
        $matches = collect();

        foreach ($file as $row) {
            if (! is_array($row) || $row === [null] || $row === false) {
                continue;
            }

            if ($headerMap === null) {
                $headerMap = $this->buildHeaderMap($row);
                continue;
            }

            $currentCodigoPostal = str_pad(trim((string) ($row[$headerMap['codigo_postal'] ?? -1] ?? '')), 5, '0', STR_PAD_LEFT);

            if ($currentCodigoPostal !== $codigoPostal) {
                continue;
            }

            $asentamiento = trim((string) ($row[$headerMap['asentamiento'] ?? -1] ?? ''));
            $municipio = trim((string) ($row[$headerMap['municipio'] ?? -1] ?? ''));
            $estado = trim((string) ($row[$headerMap['estado'] ?? -1] ?? ''));

            if ($asentamiento === '' || $municipio === '' || $estado === '') {
                continue;
            }

            $matches->push((object) [
                'codigo_postal' => $codigoPostal,
                'asentamiento' => $asentamiento,
                'tipo_asentamiento' => $this->valueFromRow($row, $headerMap, 'tipo_asentamiento'),
                'municipio' => $municipio,
                'estado' => $estado,
                'ciudad' => $this->valueFromRow($row, $headerMap, 'ciudad'),
                'zona' => $this->valueFromRow($row, $headerMap, 'zona'),
            ]);
        }

        if ($matches->isEmpty()) {
            return null;
        }

        return $this->responseFromResults($matches, 'archivo_local_sepomex');
    }

    protected function searchInCopomex(string $codigoPostal): ?JsonResponse
    {
        $token = config('services.copomex.token');

        if (! $token) {
            return null;
        }

        $response = Http::baseUrl((string) config('services.copomex.base_url'))
            ->timeout((int) config('services.copomex.timeout', 8))
            ->get("/query/info_cp/{$codigoPostal}", [
                'token' => $token,
            ]);

        if (! $response->ok()) {
            return null;
        }

        $payload = $response->json();

        if (! is_array($payload) || $payload === []) {
            return response()->json([
                'message' => 'No se encontro informacion para el codigo postal solicitado.',
            ], 404);
        }

        $first = data_get($payload, '0.response');

        if (! is_array($first)) {
            return response()->json([
                'message' => 'No se encontro informacion para el codigo postal solicitado.',
            ], 404);
        }

        $colonias = collect($payload)
            ->map(fn (array $item) => data_get($item, 'response'))
            ->filter()
            ->map(fn (array $item) => [
                'nombre' => $item['asentamiento'] ?? null,
                'tipo_asentamiento' => $item['tipo_asentamiento'] ?? null,
                'zona' => $item['zona'] ?? null,
            ])
            ->filter(fn (array $item) => ! empty($item['nombre']))
            ->unique('nombre')
            ->values();

        return response()->json([
            'data' => [
                'codigo_postal' => $codigoPostal,
                'estado' => $first['estado'] ?? null,
                'municipio' => $first['municipio'] ?? null,
                'ciudad' => $first['ciudad'] ?? ($first['municipio'] ?? null),
                'colonias' => $colonias,
                'source' => 'copomex_fallback',
            ],
        ]);
    }

    protected function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');
        $firstLine = $handle ? (string) fgets($handle) : '';

        if (is_resource($handle)) {
            fclose($handle);
        }

        $candidates = ['|', ';', ',', "\t"];
        $bestDelimiter = '|';
        $bestCount = -1;

        foreach ($candidates as $candidate) {
            $count = substr_count($firstLine, $candidate);

            if ($count > $bestCount) {
                $bestDelimiter = $candidate;
                $bestCount = $count;
            }
        }

        return $bestDelimiter;
    }

    protected function buildHeaderMap(array $row): array
    {
        $map = [];

        foreach ($row as $index => $value) {
            $normalized = $this->normalizeHeader((string) $value);

            $field = match ($normalized) {
                'd_codigo', 'codigo_postal', 'cp' => 'codigo_postal',
                'd_asenta', 'asentamiento' => 'asentamiento',
                'd_tipo_asenta', 'tipo_asentamiento' => 'tipo_asentamiento',
                'd_mnpio', 'municipio' => 'municipio',
                'd_estado', 'estado' => 'estado',
                'd_ciudad', 'ciudad' => 'ciudad',
                'd_zona', 'zona' => 'zona',
                default => null,
            };

            if ($field) {
                $map[$field] = $index;
            }
        }

        return $map;
    }

    protected function valueFromRow(array $row, array $headerMap, string $field): ?string
    {
        if (! isset($headerMap[$field])) {
            return null;
        }

        $value = trim((string) ($row[$headerMap[$field]] ?? ''));

        return $value === '' ? null : $value;
    }

    protected function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return Str::of($header)
            ->trim()
            ->lower()
            ->ascii()
            ->replace(' ', '_')
            ->replace('-', '_')
            ->toString();
    }
}
