<?php

namespace App\DTOs;

class RecipientDTO
{
    public function __construct(
        public string $recipientUuid,
        public ?string $recipientEmail,
        public ?string $recipientPhone,
        public ?string $recipientDeviceToken,
    )
    {
    }
}
