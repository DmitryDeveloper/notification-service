<?php

namespace App\Templates;

class EmailTemplate extends NotificationTemplate
{
    public function __construct(
        private readonly string $recipientAddress,
        private readonly string $subject,
        private readonly string $message
    )
    {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
    }
}
