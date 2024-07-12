<?php

namespace App\DTOs;

readonly class NotificationPayloadDTO
{
    public function __construct(
        public ?string $subject,
        public ?string $message,
    )
    {
    }
}
