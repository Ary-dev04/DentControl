<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $projectId;
    private string $credentialsPath;

    public function __construct()
    {
        $this->projectId       = config('services.firebase.project_id');
        $this->credentialsPath = base_path(config('services.firebase.credentials'));
    }

    private function getAccessToken(): string
    {
        $scopes      = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $this->credentialsPath);
        $token       = $credentials->fetchAuthToken();
        return $token['access_token'];
    }

    public function enviar(string $fcmToken, string $titulo, string $cuerpo, array $data = []): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post(
                "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                [
                    'message' => [
                        'token'        => $fcmToken,
                        'notification' => [
                            'title' => $titulo,
                            'body'  => $cuerpo,
                        ],
                        'data'         => array_merge($data, [
                            'title' => $titulo,
                            'body'  => $cuerpo,
                        ]),
                        'android'      => [
                            'priority'     => 'high',
                            'notification' => [
                                'channel_id'  => 'dentcontrol_channel',
                                'sound'       => 'default',
                            ],
                        ],
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::error('FCM error: ' . $response->body());
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('FCM exception: ' . $e->getMessage());
            return false;
        }
    }
}