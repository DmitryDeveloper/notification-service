<?php

namespace Tests\Unit;

use App\Aggregates\Notification;
use App\Entities\Channel;
use App\Exceptions\SendingNotificationException;
use App\Jobs\NotificationJob;
use Mockery;
use PHPUnit\Framework\TestCase;

class NotificationJobTest extends TestCase
{
    public function testHandleSuccess(): void
    {
        $notificationMock = Mockery::mock(Notification::class);
        $notificationMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $job = new NotificationJob($notificationMock);

        // Assert no exception is thrown
        $job->handle();
    }

    public function testHandleFailure(): void
    {
        $this->expectException(SendingNotificationException::class);

        $channelMock = Mockery::mock(Channel::class);
        $channelMock->shouldReceive('getCode')
            ->once()
            ->andReturn('email');
        $notificationMock = Mockery::mock(Notification::class);
        $notificationMock->shouldReceive('send')
            ->once()
            ->andReturn(false);
        $notificationMock->shouldReceive('getChannel')
            ->once()
            ->andReturn($channelMock);
        $notificationMock->shouldReceive('fail')
            ->once();

        $job = new NotificationJob($notificationMock);

        $job->handle();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
