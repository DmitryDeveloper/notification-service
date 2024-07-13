<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case PENDING = 'pending';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
