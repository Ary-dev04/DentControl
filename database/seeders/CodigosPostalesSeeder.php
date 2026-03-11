<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodigosPostalesSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/CPdescarga.txt');

        if (!file_exists($file)) {
            $this->command->error("No se encontró el archivo en {$file}");
            return;
        }

        $handle = fopen($file, "r");
        $dataToInsert = [];
        $chunkSize = 1000; // Cantidad de registros a insertar por lote
        
        // Saltamos las dos primeras líneas porque SEPOMEX suele poner texto legal y los encabezados ahí
        fgets($handle); 
        fgets($handle);

        $this->command->info('Importando códigos postales, esto puede tardar un minuto...');

        while (($line = fgets($handle)) !== false) {
            // SEPOMEX usa ISO-8859-1, lo pasamos a UTF-8 para que los acentos se vean bien en tu app y web
            $line = mb_convert_encoding($line, 'UTF-8', 'ISO-8859-1');
            $row = explode('|', $line);

            // Verificamos que la fila tenga datos válidos (al menos el CP en la posición 0)
            if (isset($row[0]) && trim($row[0]) !== '') {
                $dataToInsert[] = [
                    'codigo_postal'     => trim($row[0]),
                    'asentamiento'      => trim($row[1]),
                    'tipo_asentamiento' => trim($row[2]),
                    'municipio'         => trim($row[3]),
                    'estado'            => trim($row[4]),
                    'ciudad'            => trim($row[5] ?? ''),
                ];

                // Cuando juntamos 1000 registros, los insertamos de golpe
                if (count($dataToInsert) >= $chunkSize) {
                    DB::table('codigos_postales')->insert($dataToInsert);
                    $dataToInsert = []; // Limpiamos el arreglo
                }
            }
        }

        // Insertamos los que hayan sobrado al final
        if (count($dataToInsert) > 0) {
            DB::table('codigos_postales')->insert($dataToInsert);
        }

        fclose($handle);
        $this->command->info('¡Códigos postales importados con éxito!');
    }
}