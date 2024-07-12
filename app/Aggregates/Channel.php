<?php

namespace App\Aggregates;

use App\Exceptions\TemplateIsNotSetException;
use Exception;
use App\Entities\Providers\BaseProvider;
use Illuminate\Support\Facades\Log;
use App\Templates\NotificationTemplate;

class Channel
{
    private array $providers = [];
    private NotificationTemplate $template;

    /**
     * @param string $code
     * @param bool $isEnabled
     */
    public function __construct(private readonly string $code, private readonly bool $isEnabled)
    {
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param BaseProvider $provider
     * @return void
     */
    public function addProvider(BaseProvider $provider): void
    {
        $this->providers[] = $provider;
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
        foreach ($this->providers as $provider) {
            if ($provider->send($this->getNotificationTemplate())) {
                Log::info(sprintf('Notification was sent: channel %s, provider %s', $this->code, get_class($provider)));
                $success = true;
                break;
            }
        }

        return $success;
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
}
