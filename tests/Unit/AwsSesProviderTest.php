<?php

namespace Tests\Unit;

use App\Entities\Providers\AwsSesProvider;
use App\Templates\EmailTemplate;
use Aws\Ses\SesClient;
use Aws\Result;
use Mockery;
use Tests\TestCase;

class AwsSesProviderTest extends TestCase
{
    public function testSendEmailSuccess(): void
    {
        $notificationTemplateMock = Mockery::mock(EmailTemplate::class);
        $notificationTemplateMock->shouldReceive('getRecipientAddress')
            ->once()
            ->andReturn('recipient@example.com');

        $notificationTemplateMock->shouldReceive('getSubject')
            ->once()
            ->andReturn('Test Subject');

        $notificationTemplateMock->shouldReceive('getMessage')
            ->once()
            ->andReturn('Test Message');

        $sesClientMock = Mockery::mock(SesClient::class);
        $sesClientMock->shouldReceive('sendEmail')
            ->once()
            ->andReturn(new Result(['MessageId' => '123456789']));

        $awsSesProvider = new AwsSesProvider($sesClientMock);

        $result = $awsSesProvider->send($notificationTemplateMock);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
