<?php

namespace App\Entities\Providers;

use App\Templates\NotificationTemplate;
use App\Templates\SMSTemplate;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

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
     * @throws TwilioException
     */
    public function send(NotificationTemplate $notification): bool
    {
        $message = $this->client->messages->create(
            $notification->getPhone(),
            [
                'from' => config('services.twilio.from'),
                'body' => $notification->getMessage(),
            ]
        );

        return $message->sid !== null;
    }
}
