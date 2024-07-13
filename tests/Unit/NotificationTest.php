<?php

namespace Tests\Unit;

use App\Aggregates\Notification;
use App\Entities\Channel;
use App\Entities\Providers\BaseProvider;
use App\Models\Notification as NotificationModel;
use App\Repositories\NotificationRepositoryInterface;
use App\Templates\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSendSuccess()
    {
        $templateMock = Mockery::mock(NotificationTemplate::class);
        $providerMock = Mockery::mock(BaseProvider::class);
        $providerMock->shouldReceive('send')->once()->andReturn(true);
        $channelMock = Mockery::mock(Channel::class);
        $channelMock->shouldReceive('getProviders')->andReturn([$providerMock]);
        $channelMock->shouldReceive('getCode')->andReturn('test_channel');
        $repositoryMock = Mockery::mock(NotificationRepositoryInterface::class);
        $repositoryMock->shouldReceive('complete')->once();
        $notificationModel = Mockery::mock(NotificationModel::class);
        $notificationModel->shouldReceive('getId')->once()->andReturn(1);

        Log::shouldReceive('info')->once();

        $notification = new Notification($notificationModel->getId(), $channelMock, $repositoryMock);
        $notification->addNotificationTemplate($templateMock);

        $this->assertTrue($notification->send());
    }

    public function testSendFail()
    {
        $templateMock = Mockery::mock(NotificationTemplate::class);
        $providerMock = Mockery::mock(BaseProvider::class);
        $providerMock->shouldReceive('send')->once()->andReturn(false);
        $channelMock = Mockery::mock(Channel::class);
        $channelMock->shouldReceive('getProviders')->andReturn([$providerMock]);
        $channelMock->shouldReceive('getCode')->andReturn('test_channel');

        $notification = new Notification(
            1,
            $channelMock,
            Mockery::mock(NotificationRepositoryInterface::class)
        );
        $notification->addNotificationTemplate($templateMock);

        $this->assertFalse($notification->send());
    }

    public function testGetChannel()
    {
        $channelMock = Mockery::mock(Channel::class);

        $notification = new Notification(
            1,
            $channelMock,
            Mockery::mock(NotificationRepositoryInterface::class)
        );

        $this->assertSame($channelMock, $notification->getChannel());
    }

    public function testAddNotificationTemplate()
    {
        $templateMock = Mockery::mock(NotificationTemplate::class);

        $notification = new Notification(
            1,
            Mockery::mock(Channel::class),
            Mockery::mock(NotificationRepositoryInterface::class)
        );
        $notification->addNotificationTemplate($templateMock);

        $this->assertSame($templateMock, $notification->getNotificationTemplate());
    }

    public function testGetNotificationTemplate()
    {
        $templateMock = Mockery::mock(NotificationTemplate::class);

        $notification = new Notification(
            1,
            Mockery::mock(Channel::class),
            Mockery::mock(NotificationRepositoryInterface::class)
        );
        $notification->addNotificationTemplate($templateMock);

        $this->assertSame($templateMock, $notification->getNotificationTemplate());
    }
}
