<?php

namespace App\Entities\Providers;

use App\Templates\EmailTemplate;
use App\Templates\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;
use Exception;

/**
 * Not tested with real account
 */
class SendGridProvider extends BaseProvider
{
    protected SendGrid $client;

    public function __construct()
    {
        $this->client = new SendGrid(config('services.sendgrid.api_key'));
    }

    /**
     * @param EmailTemplate $notification
     * @return bool
     */
    public function send(NotificationTemplate $notification): bool
    {
        try {
            $email = new Mail();
            $email->setFrom(config('services.sendgrid.from'), "Your Name");
            $email->setSubject($notification->getSubject());
            $email->addTo($notification->getRecipientAddress());
            $email->addContent("text/plain", $notification->getMessage());

            $response = $this->client->send($email);

            return in_array($response->statusCode(), [200, 202]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}
