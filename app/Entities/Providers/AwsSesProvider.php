<?php

namespace App\Entities\Providers;

use App\Templates\EmailTemplate;
use Exception;
use Aws\Ses\SesClient;
use App\Templates\NotificationTemplate;
use Illuminate\Support\Facades\Log;

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
        try {
            $this->client->sendEmail([
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
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }

        return true;
    }
}
