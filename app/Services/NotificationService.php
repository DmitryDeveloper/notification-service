<?php

namespace App\Services;

use Exception;
use App\DTOs\NotificationDTO;
use App\Exceptions\ChannelDisabled;
use App\Factories\TemplateFactory;
use App\Jobs\NotificationJob;
use App\Repositories\ChannelRepositoryInterface;
use Illuminate\Support\Facades\Log;

readonly class NotificationService
{
    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    /**
     * @param NotificationDTO $notificationDTO
     * @return void
     */
    public function send(NotificationDTO $notificationDTO): void
    {
        foreach ($notificationDTO->channels as $channelCode) {
            try {
                $channel = $this->channelRepository->getByCode($channelCode);

                //TODO requirement missing, "Send the same notification via several different channels".
                // What if one channel does not exist or disabled or payload is broken for specific channel?
                // Should we continue with rest passed channels or we should rollback transaction?
                if (!$channel->isEnabled()) {
                    throw new ChannelDisabled($channelCode);
                }

                $channel->addNotificationTemplate(TemplateFactory::createTemplate($channel, $notificationDTO));
                NotificationJob::dispatch($channel);
            } catch (Exception $e) {
                Log::error(sprintf('Channel %s cannot be processed, message %s', $channelCode, $e->getMessage()));
            }
        }
    }
}
