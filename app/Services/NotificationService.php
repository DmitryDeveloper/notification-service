<?php

namespace App\Services;

use Exception;
use App\DTOs\NotificationDTO;
use App\Exceptions\ChannelDisabled;
use App\Factories\TemplateFactory;
use App\Jobs\NotificationJob;
use App\Repositories\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Log;

readonly class NotificationService
{
    /**
     * @param NotificationRepositoryInterface $channelRepository
     */
    public function __construct(private NotificationRepositoryInterface $channelRepository)
    {
    }

    /**
     * @param NotificationDTO $dto
     * @return void
     */
    public function send(NotificationDTO $dto): void
    {
        foreach ($dto->channels as $channelCode) {
            try {
                $notificationAggregate = $this->channelRepository->create($dto, $channelCode);

                //TODO not entirely clear requirement: "Send the same notification via several different channels".
                // What if one channel does not exist or disabled or payload is broken for specific channel?
                // Should we continue with rest passed channels or we should rollback transaction?
                // I decided to use this option "continue with rest passed channels"
                if (!$notificationAggregate->getChannel()->isEnabled()) {
                    throw new ChannelDisabled($channelCode);
                }

                $notificationAggregate->addNotificationTemplate(
                    TemplateFactory::createTemplate($notificationAggregate->getChannel(), $dto)
                );
                NotificationJob::dispatch($notificationAggregate);
            } catch (Exception $e) {
                Log::error(sprintf('Channel %s cannot be processed, message %s', $channelCode, $e->getMessage()));
            }
        }
    }
}
