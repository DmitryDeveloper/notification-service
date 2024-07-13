<?php

namespace App\Aggregates;

use App\Entities\Channel;
use App\Entities\Providers\BaseProvider;
use App\Exceptions\TemplateIsNotSetException;
use App\Repositories\NotificationRepositoryInterface;
use App\Templates\NotificationTemplate;
use Illuminate\Support\Facades\Log;

class Notification
{
    private int $id;
    private Channel $channel;
    private NotificationTemplate $template;
    private NotificationRepositoryInterface $repository;

    public function __construct(int $id, Channel $channel, NotificationRepositoryInterface $repository)
    {
        $this->id = $id;
        $this->channel = $channel;
        $this->repository = $repository;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @param NotificationTemplate $createTemplate
     * @return void
     */
    public function addNotificationTemplate(NotificationTemplate $createTemplate): void
    {
        $this->template = $createTemplate;
    }

    /**
     * @return NotificationTemplate
     * @throws TemplateIsNotSetException
     */
    public function getNotificationTemplate(): NotificationTemplate
    {
        if (!$this->template) {
            throw new TemplateIsNotSetException();
        }

        return $this->template;
    }

    /**
     * @return bool
     * @throws TemplateIsNotSetException
     */
    public function send(): bool
    {
        $success = false;
        /**
         * @var BaseProvider $provider
         */
        foreach ($this->getChannel()->getProviders() as $provider) {
            if ($provider->send($this->getNotificationTemplate())) {
                Log::info(sprintf(
                    'Notification was sent: channel %s, provider %s',
                    $this->channel->getCode(), get_class($provider)
                ));
                $this->complete();
                $success = true;
                break;
            }
        }

        return $success;
    }

    public function fail(): void
    {
        $this->repository->fail($this->id);
    }

    public function complete(): void
    {
        $this->repository->complete($this->id);
    }
}
