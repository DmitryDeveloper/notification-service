<?php

namespace App\Providers;

use App\Repositories\NotificationRepository;
use App\Repositories\NotificationRepositoryInterface;
use Aws\Ses\SesClient;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client as TwilioClient;
use SendGrid;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);

        $this->app->singleton(SesClient::class, function ($app) {
            return new SesClient([
                'version' => 'latest',
                'region' => config('services.ses.region'),
                'credentials' => [
                    'key' => config('services.ses.key'),
                    'secret' => config('services.ses.secret'),
                ],
            ]);
        });

        $this->app->singleton(TwilioClient::class, function ($app) {
            return new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token'),
            );
        });

        $this->app->singleton(SendGrid::class, function ($app) {
            return new SendGrid(config('services.sendgrid.api_key'));
        });
    }
}
