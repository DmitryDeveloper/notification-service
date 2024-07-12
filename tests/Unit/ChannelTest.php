<?php

namespace Tests\Unit;

use App\Aggregates\Channel;
use App\Entities\Providers\AwsSesProvider;
use App\Entities\Providers\TwilioProvider;
use App\Templates\NotificationTemplate;
use Exception;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    public function testSendWithAllProvidersSuccessfully(): void
    {
        $awsSesProviderMock = Mockery::mock(AwsSesProvider::class);
        $twilioProviderMock = Mockery::mock(TwilioProvider::class);

        $awsSesProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $twilioProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $templateMock = Mockery::mock(NotificationTemplate::class);

        $channel = new Channel('email', true);
        $channel->addProvider($awsSesProviderMock);
        $channel->addProvider($twilioProviderMock);
        $channel->addNotificationTemplate($templateMock);

        $this->assertTrue($channel->send());
    }

    public function testSendWithOneProviderFailure(): void
    {
        Log::shouldReceive('error')
            ->once();

        $awsSesProviderMock = Mockery::mock(AwsSesProvider::class);
        $twilioProviderMock = Mockery::mock(TwilioProvider::class);

        $awsSesProviderMock->shouldReceive('send')
            ->once()
            ->andThrow(new Exception('Provider 1 failed'));

        $twilioProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $templateMock = Mockery::mock(NotificationTemplate::class);

        $channel = new Channel('email', true);
        $channel->addProvider($awsSesProviderMock);
        $channel->addProvider($twilioProviderMock);
        $channel->addNotificationTemplate($templateMock);

        $this->assertTrue($channel->send());
    }

    public function testSendWithAllProvidersFailure(): void
    {
        Log::shouldReceive('error')
            ->twice();

        $awsSesProviderMock = Mockery::mock(AwsSesProvider::class);
        $twilioProviderMock = Mockery::mock(TwilioProvider::class);

        $awsSesProviderMock->shouldReceive('send')
            ->once()
            ->andThrow(new Exception('Provider 1 failed'));

        $twilioProviderMock->shouldReceive('send')
            ->once()
            ->andThrow(new Exception('Provider 2 failed'));

        $templateMock = Mockery::mock(NotificationTemplate::class);

        $channel = new Channel('email', true);
        $channel->addProvider($awsSesProviderMock);
        $channel->addProvider($twilioProviderMock);
        $channel->addNotificationTemplate($templateMock);

        $this->assertFalse($channel->send());
    }

    public function testSendWithoutTemplate(): void
    {
        $channel = new Channel('email', true);

        $this->assertFalse($channel->send());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
