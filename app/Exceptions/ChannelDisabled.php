<?php

namespace App\Exceptions;

use Exception;

class ChannelDisabled extends Exception
{
    public function __construct(string $channelCode)
    {
        parent::__construct("Channel $channelCode is disabled.");
    }
}
