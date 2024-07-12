<?php

namespace Tests\Unit;

use App\Aggregates\Channel;
use App\Exceptions\SendingNotificationException;
use App\Jobs\NotificationJob;
use Mockery;
use PHPUnit\Framework\TestCase;

class NotificationJobTest extends TestCase
{
    public function testHandleSuccess(): void
    {
        $channelMock = Mockery::mock(Channel::class);
        $channelMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $job = new NotificationJob($channelMock);

        // Assert no exception is thrown
        $job->handle();
    }

    public function testHandleFailure(): void
    {
        $this->expectException(SendingNotificationException::class);

        $channelMock = Mockery::mock(Channel::class);
        $channelMock->shouldReceive('send')
            ->once()
            ->andReturn(false);

        $channelMock->shouldReceive('getCode')
            ->once()
            ->andReturn('email');

        $job = new NotificationJob($channelMock);

        $job->handle();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
