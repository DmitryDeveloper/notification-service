<?php

namespace App\Jobs;

use App\Aggregates\Channel;
use App\Exceptions\SendingNotificationException;
use App\Exceptions\TemplateIsNotSetException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Channel $channel;

    /**
     * Create a new job instance.
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return void
     * @throws SendingNotificationException
     * @throws TemplateIsNotSetException
     */
    public function handle(): void
    {
        $result = $this->channel->send();

        if (!$result) {
            throw new SendingNotificationException($this->channel->getCode());
        }
    }

    /**
     * Determine number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 5;
    }

    /**
     * Get the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }
}
