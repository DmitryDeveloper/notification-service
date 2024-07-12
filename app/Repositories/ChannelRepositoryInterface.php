<?php

namespace App\Repositories;

use App\Aggregates\Channel;

interface ChannelRepositoryInterface
{
    /**
     * @param string $code
     * @return Channel
     */
    public function getByCode(string $code): Channel;
}
