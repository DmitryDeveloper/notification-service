<?php

return [
    'send_notifications' => [
        'requests_limit' => env('THROTTLE_SEND_NOTIFICATIONS_REQUESTS_LIMIT', 300),
        'window_in_minutes' => env('THROTTLE_SEND_NOTIFICATIONS_WINDOW_IN_MINUTES', 60),
    ],
];
