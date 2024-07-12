<?php

namespace Tests\Unit\Factories;

use App\Aggregates\Channel;
use App\DTOs\NotificationDTO;
use App\DTOs\NotificationPayloadDTO;
use App\DTOs\RecipientDTO;
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
            new RecipientDTO('recipient-uuid', 'recipient@example.com'),
            new NotificationPayloadDTO('Test Subject', 'Test Message')
        );
    }

    public function testCreateEmailTemplate(): void
    {
        $channel = new Channel('email', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(EmailTemplate::class, $template);
        $this->assertEquals('Test Subject', $template->getSubject());
        $this->assertEquals('Test Message', $template->getMessage());
        $this->assertEquals('recipient@example.com', $template->getRecipientAddress());
    }

    public function testCreateSMSTemplate(): void
    {
        $channel = new Channel('sms', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(SMSTemplate::class, $template);
        $this->assertEquals('recipient@example.com', $template->getPhone());
        $this->assertEquals('Test Message', $template->getMessage());
    }

    public function testCreatePushTemplate(): void
    {
        $channel = new Channel('push', true);
        $template = TemplateFactory::createTemplate($channel, $this->notificationDTO);

        $this->assertInstanceOf(PushTemplate::class, $template);
        $this->assertEquals('Test Subject', $template->getTitle());
        $this->assertEquals('Test Message', $template->getMessage());
        $this->assertEquals('recipient@example.com', $template->getDeviceToken());
    }

    public function testCreateTemplateWithInvalidChannel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown channel: invalid');

        $channel = new Channel('invalid', true);
        TemplateFactory::createTemplate($channel, $this->notificationDTO);
    }
}
