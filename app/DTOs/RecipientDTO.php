<?php

namespace App\DTOs;

class RecipientDTO
{
    public function __construct(
        public string $recipientUuid,
        public string $recipientAddress,
    )
    {
    }
}
