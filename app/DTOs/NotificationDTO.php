<?php

namespace App\DTOs;

readonly class NotificationDTO
{
    public function __construct(
        public array  $channels,
        public string $senderUuid,
        public RecipientDTO $recipient,
        public NotificationPayloadDTO $payload,
    )
    {
    }
}
