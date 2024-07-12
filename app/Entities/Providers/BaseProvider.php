<?php

namespace App\Entities\Providers;

use App\Templates\NotificationTemplate;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class BaseProvider
{
    private static array $map = [
        'aws-ses' => AwsSesProvider::class,
        'twilio' => TwilioProvider::class,
        'send-grid' => SendGridProvider::class,
        //etc
    ];

    /**
     * @param NotificationTemplate $notification
     * @return bool
     */
    abstract public function send(NotificationTemplate $notification): bool;

    /**
     * @param string $code
     * @return BaseProvider
     * @throws BindingResolutionException
     */
    public static function getProviderClass(string $code): BaseProvider
    {
        return app()->make(self::$map[$code]);
    }
}
