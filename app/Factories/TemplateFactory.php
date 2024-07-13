<?php

namespace App\Factories;

use App\DTOs\NotificationDTO;
use App\Entities\Channel;
use App\Templates\EmailTemplate;
use App\Templates\NotificationTemplate;
use App\Templates\PushTemplate;
use App\Templates\SMSTemplate;
use InvalidArgumentException;

class TemplateFactory
{
    /**
     * @param Channel $channel
     * @param NotificationDTO $notificationDTO
     * @return NotificationTemplate
     */
    public static function createTemplate(Channel $channel, NotificationDTO $notificationDTO): NotificationTemplate
    {
        return match ($channel->getCode()) {
            'email' => new EmailTemplate(
                $notificationDTO->recipient->recipientAddress,
                $notificationDTO->payload->subject,
                $notificationDTO->payload->message
            ),
            'sms' => new SMSTemplate(
                $notificationDTO->recipient->recipientAddress,
                $notificationDTO->payload->message
            ),
            'push' => new PushTemplate(
                $notificationDTO->recipient->recipientAddress,
                $notificationDTO->payload->subject,
                $notificationDTO->payload->message
            ),
            default => throw new InvalidArgumentException('Unknown channel: ' . $channel->getCode()),
        };
    }
}
