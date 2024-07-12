<?php

namespace Tests\Unit;

use App\Aggregates\Channel;
use App\Entities\Providers\AwsSesProvider;
use App\Entities\Providers\TwilioProvider;
use App\Templates\NotificationTemplate;
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
            ->never();

        $templateMock = Mockery::mock(NotificationTemplate::class);

        $channel = new Channel('email', true);
        $channel->addProvider($awsSesProviderMock);
        $channel->addProvider($twilioProviderMock);
        $channel->addNotificationTemplate($templateMock);

        $this->assertTrue($channel->send());
    }

    public function testSendWithOneProviderFailure(): void
    {
        $awsSesProviderMock = Mockery::mock(AwsSesProvider::class);
        $twilioProviderMock = Mockery::mock(TwilioProvider::class);

        $awsSesProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(false);

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
        $awsSesProviderMock = Mockery::mock(AwsSesProvider::class);
        $twilioProviderMock = Mockery::mock(TwilioProvider::class);

        $awsSesProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(false);

        $twilioProviderMock->shouldReceive('send')
            ->once()
            ->andReturn(false);

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
