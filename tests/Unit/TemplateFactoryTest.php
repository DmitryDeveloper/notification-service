<?php

namespace Tests\Unit;

use App\DTOs\NotificationDTO;
use App\DTOs\NotificationPayloadDTO;
use App\DTOs\RecipientDTO;
use App\Entities\Channel;
use App\Factories\TemplateFactory;
use App\Templates\EmailTemplate;
use App\Templates\PushTemplate;
use App\Templates\SMSTemplate;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TemplateFactoryTest extends TestCase
{
    protected NotificationDTO $notificationDTO;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationDTO = new NotificationDTO(
            ['email', 'sms', 'push'],
            'sender-uuid',
            new RecipientDTO(
                'recipient-uuid',
                'recipient@example.com',
                '+1234567890',
                'XXXXXXXXXXXXXXXX'
            ),
            new NotificationPayloadDTO('Test Subject', 'Test Message')
        );
    }

    public function testCreateEmailTemplate(): void
    {
        $channel = new Channel('email', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(EmailTemplate::class, $template);
        $this->assertEquals($this->notificationDTO->payload->subject, $template->getSubject());
        $this->assertEquals($this->notificationDTO->payload->message, $template->getMessage());
        $this->assertEquals($this->notificationDTO->recipient->recipientEmail, $template->getRecipientAddress());
    }

    public function testCreateSMSTemplate(): void
    {
        $channel = new Channel('sms', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(SMSTemplate::class, $template);
        $this->assertEquals($this->notificationDTO->recipient->recipientPhone, $template->getPhone());
        $this->assertEquals($this->notificationDTO->payload->message, $template->getMessage());
    }

    public function testCreatePushTemplate(): void
    {
        $channel = new Channel('push', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(PushTemplate::class, $template);
        $this->assertEquals($this->notificationDTO->payload->subject, $template->getTitle());
        $this->assertEquals($this->notificationDTO->payload->message, $template->getMessage());
        $this->assertEquals($this->notificationDTO->recipient->recipientDeviceToken, $template->getDeviceToken());
    }

    public function testCreateTemplateWithInvalidChannel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown channel: invalid');

        $channel = new Channel('invalid', true);
        TemplateFactory::createTemplate($channel, $this->notificationDTO);
    }
}
