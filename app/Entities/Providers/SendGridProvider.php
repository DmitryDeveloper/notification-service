<?php

namespace App\Entities\Providers;

use App\Templates\NotificationTemplate;
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridProvider extends BaseProvider
{
    protected SendGrid $client;

    public function __construct()
    {
        $this->client = new SendGrid(config('services.sendgrid.api_key'));
    }

    public function send(NotificationTemplate $notification): bool
    {
        $email = new Mail();
        $email->setFrom(config('services.sendgrid.from'), "Your Name");
        $email->setSubject($notification->getSubject());
        $email->addTo($notification->getRecipientAddress());
        $email->addContent("text/plain", $notification->getMessage());

        $response = $this->client->send($email);

        return in_array($response->statusCode(), [200, 202]);
    }
}
