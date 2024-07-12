<?php

namespace App\Exceptions;

use Exception;

class SendingNotificationException extends Exception
{
    public function __construct(string $channelCode)
    {
        parent::__construct("Failed to send notification, channel $channelCode");
    }
}
