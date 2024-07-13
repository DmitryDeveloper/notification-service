<?php

namespace App\Repositories;

use App\Aggregates\Notification;
use App\DTOs\NotificationDTO;
use App\Entities\Channel;
use App\Entities\Providers\BaseProvider;
use App\Enums\NotificationStatus;
use App\Models\Channel as ChannelModel;
use App\Models\Notification as NotificationModel;
use App\Models\Provider;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private NotificationModel $notificationModel, private ChannelModel $channelModel) {

    }

    /**
     * @param NotificationDTO $notificationDTO
     * @param string $channelCode
     * @return Notification
     * @throws BindingResolutionException
     */
    public function create(NotificationDTO $notificationDTO, string $channelCode): Notification
    {
        $channelModel = $this->channelModel->where('code', $channelCode)->firstOrFail();
        $providersRecords = $channelModel->hasMany(Provider::class)->where('is_enabled', true)->get();
        $channel = new Channel($channelCode, $channelModel->is_enabled);

        foreach ($providersRecords as $record) {
            $channel->addProvider(BaseProvider::getProviderClass($record->code));
        }

        $model = $this->notificationModel->create([
            'sender' => $notificationDTO->senderUuid,
            'recipient' => $notificationDTO->recipient->recipientUuid,
            'channel_id' => $channelModel->id,
            'status' => NotificationStatus::PENDING->value
        ]);

        return new Notification($model->getId(), $channel, $this);
    }

    public function fail(int $id): void
    {
        $this->notificationModel->where('id', $id)->update([
            'status' => NotificationStatus::FAILED->value,
        ]);
    }

    public function complete(int $id): void
    {
        $this->notificationModel->where('id', $id)->update([
            'status' => NotificationStatus::COMPLETED->value,
        ]);
    }
}
