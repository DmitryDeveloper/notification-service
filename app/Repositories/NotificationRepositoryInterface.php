<?php

namespace App\Repositories;

use App\Aggregates\Notification;
use App\DTOs\NotificationDTO;

interface NotificationRepositoryInterface
{
    /**
     * @param NotificationDTO $notificationDTO
     * @param string $channelCode
     * @return Notification
     */
    public function create(NotificationDTO $notificationDTO, string $channelCode): Notification;

    /**
     * @param int $id
     * @return void
     */
    public function fail(int $id): void;

    /**
     * @param int $id
     * @return void
     */
    public function complete(int $id): void;
}
