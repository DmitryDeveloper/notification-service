<?php

namespace App\Entities\Providers;

use App\Templates\EmailTemplate;
use Exception;
use Aws\Ses\SesClient;
use App\Templates\NotificationTemplate;
use Illuminate\Support\Facades\Log;

/**
 * Not tested with real account
 * AWS SES requires to have real own domain so this is not free
 */
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

    public function __sleep() {
        return [];
    }

    public function __wakeup() {
        //Since we can't serialize AWS SES Client we need to re-create it when we unserialise
        //https://laracasts.com/discuss/channels/laravel/an-error-occurredinstances-of-awss3s3client-cannot-be-serialized
        $this->client = app(SesClient::class);
    }
}
