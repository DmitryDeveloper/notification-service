<?php

namespace App\Entities\Providers;

use Exception;
use App\Templates\NotificationTemplate;
use App\Templates\PushTemplate;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Not tested with real account
 */
class PushyProvider
{
    private Client $client;
    private string $clientUrl;
    private string $apiKey;
    private string $appId;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientUrl = config('services.pushy.url');
        $this->apiKey = config('services.pushy.api_key');
        $this->appId = config('services.pushy.app_id');
    }

    /**
     * @param PushTemplate $notification
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(NotificationTemplate $notification): bool
    {
        try {
            $response = $this->client->post($this->clientUrl . '/push?api_key=' . $this->apiKey, [
                'json' => [
                    'to' => $notification->getDeviceToken(),
                    'data' => [
                        'message' => $notification->getMessage(),
                    ],
                    'notification' => [
                        'title' => $notification->getTitle(),
                        'body' => $notification->getMessage(),
                    ],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}
