<?php

namespace Tests\Unit\Providers;

use App\Entities\Providers\TwilioProvider;
use App\Templates\SMSTemplate;
use Exception;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSendSuccess(): void
    {
        $smsTemplateMock = Mockery::mock(SMSTemplate::class);
        $smsTemplateMock->shouldReceive('getPhone')
            ->once()
            ->andReturn('+1111111111');

        $smsTemplateMock->shouldReceive('getMessage')
            ->once()
            ->andReturn('Test Message');

        $messageInstanceMock = Mockery::mock(MessageInstance::class);
        $messageInstanceMock->sid = 'SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

        $clientMock = Mockery::mock(Client::class);
        $clientMock->messages = Mockery::mock();
        $clientMock->messages->shouldReceive('create')
            ->once()
            ->with('+1111111111', [
                'from' => config('services.twilio.from'),
                'body' => 'Test Message',
            ])
            ->andReturn($messageInstanceMock);

        $twilioProvider = new TwilioProvider($clientMock);
        $result = $twilioProvider->send($smsTemplateMock);

        $this->assertTrue($result);
    }

    public function testSendFailure(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->with('Some error occurred');

        $smsTemplateMock = Mockery::mock(SMSTemplate::class);
        $smsTemplateMock->shouldReceive('getPhone')
            ->once()
            ->andReturn('+1111111111');

        $smsTemplateMock->shouldReceive('getMessage')
            ->once()
            ->andReturn('Test Message');

        $clientMock = Mockery::mock(Client::class);
        $clientMock->messages = Mockery::mock();
        $clientMock->messages->shouldReceive('create')
            ->once()
            ->with('+1111111111', [
                'from' => config('services.twilio.from'),
                'body' => 'Test Message',
            ])
            ->andThrow(new Exception('Some error occurred'));

        $twilioProvider = new TwilioProvider($clientMock);
        $result = $twilioProvider->send($smsTemplateMock);

        $this->assertFalse($result);
    }
}
