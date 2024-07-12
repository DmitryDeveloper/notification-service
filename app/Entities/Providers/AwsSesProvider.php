<?php

namespace App\Entities\Providers;

use App\Templates\EmailTemplate;
use Aws\Ses\SesClient;
use App\Templates\NotificationTemplate;

class AwsSesProvider extends BaseProvider
{
    private SesClient $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param EmailTemplate $notification
     * @return bool
     */
    public function send(NotificationTemplate $notification): bool
    {
        return (bool)$this->client->sendEmail([
            'Source' => config('services.ses.from'),
            'Destination' => [
                'ToAddresses' => [$notification->getRecipientAddress()],
            ],
            'Message' => [
                'Subject' => [
                    'Data' => $notification->getSubject(),
                    'Charset' => 'UTF-8',
                ],
                'Body' => [
                    'Text' => [
                        'Data' => $notification->getMessage(),
                        'Charset' => 'UTF-8',
                    ],
                ],
            ],
        ]);
    }
}
