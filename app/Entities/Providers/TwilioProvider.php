<?php

namespace App\Entities\Providers;

use App\Templates\NotificationTemplate;
use App\Templates\SMSTemplate;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Exception;

class TwilioProvider extends BaseProvider
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param SMSTemplate $notification
     * @return bool
     */
    public function send(NotificationTemplate $notification): bool
    {
        try {
            $message = $this->client->messages->create(
                $notification->getPhone(),
                [
                    'from' => config('services.twilio.from'),
                    'body' => $notification->getMessage(),
                ]
            );

            return $message->sid !== null;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}
