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

    public function testTries(): void
    {
        $channelMock = Mockery::mock(Channel::class);
        $job = new NotificationJob($channelMock);

        $this->assertEquals(5, $job->tries());
    }

    public function testBackoff(): void
    {
        $channelMock = Mockery::mock(Channel::class);
        $job = new NotificationJob($channelMock);

        $this->assertEquals([10, 30, 60, 120], $job->backoff());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
