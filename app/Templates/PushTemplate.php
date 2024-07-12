<?php

namespace App\Templates;

class PushTemplate extends NotificationTemplate
{
    public function __construct(
        private readonly string $deviceToken,
        private readonly string $title,
        private readonly string $message
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }
}
