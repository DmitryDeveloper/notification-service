<?php

namespace App\Repositories;

use App\Aggregates\Channel;
use App\Entities\Providers\BaseProvider;
use App\Models\Channel as ChannelModel;
use App\Models\Provider;

readonly class ChannelRepository implements ChannelRepositoryInterface
{
    public function __construct(private ChannelModel $model) {}

    public function getByCode(string $code): Channel
    {
        $channelModel = $this->model->where('code', $code)->firstOrFail();
        $providersRecords = $channelModel->hasMany(Provider::class)->where('is_enabled', true)->get();

        $channel = new Channel($code, $channelModel->is_enabled);

        foreach ($providersRecords as $record) {
           $channel->addProvider(BaseProvider::getProviderClass($record->code));
        }

        return $channel;
    }
}
