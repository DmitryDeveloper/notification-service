<?php

namespace Tests\Feature;

use App\Jobs\NotificationJob;
use App\Models\Channel as ChannelModel;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSendNotification(): void
    {
        Queue::fake();

        $channels = ['email', 'sms'];
        $senderUuid = $this->faker->uuid;
        $recipientUuid = $this->faker->uuid;
        $recipientAddress = $this->faker->email;
        $subject = $this->faker->sentence;
        $message = $this->faker->paragraph;

        $channelEmail = ChannelModel::factory()->create(['code' => 'email']);
        $channelSms = ChannelModel::factory()->create(['code' => 'sms']);
        Provider::factory()->create(['channel_id' => $channelEmail->id, 'is_enabled' => true, 'code' => 'aws-ses']);
        Provider::factory()->create(['channel_id' => $channelSms->id, 'is_enabled' => true, 'code' => 'twilio']);

        $response = $this->postJson('/api/send', [
            'channels' => $channels,
            'sender_uuid' => $senderUuid,
            'recipient_uuid' => $recipientUuid,
            'recipient_address' => $recipientAddress,
            'subject' => $subject,
            'message' => $message,
        ]);

        $response->assertStatus(200);

        Queue::assertPushed(NotificationJob::class, function ($job) use ($channels) {
            return in_array($job->channel->getCode(), $channels);
        });
    }

    public function testSendNotificationWithDisabledChannel(): void
    {
        Queue::fake();

        $channels = ['email', 'sms'];
        $senderUuid = $this->faker->uuid;
        $recipientUuid = $this->faker->uuid;
        $recipientAddress = $this->faker->email;
        $subject = $this->faker->sentence;
        $message = $this->faker->paragraph;

        $channelSms = ChannelModel::factory()->create(['code' => 'sms', 'is_enabled' => true]);
        // Disabled channel
        $channelEmail = ChannelModel::factory()->create(['code' => 'email', 'is_enabled' => false]);

        Provider::factory()->create(['channel_id' => $channelSms->id, 'code' => 'twilio']);
        Provider::factory()->create(['channel_id' => $channelEmail->id, 'code' => 'aws-ses']);

        $response = $this->postJson('/api/send', [
            'channels' => $channels,
            'sender_uuid' => $senderUuid,
            'recipient_uuid' => $recipientUuid,
            'recipient_address' => $recipientAddress,
            'subject' => $subject,
            'message' => $message,
        ]);

        $response->assertStatus(200);

        Queue::assertPushed(NotificationJob::class, function ($job) {
            return $job->channel->getCode() === 'sms';
        });
        Queue::assertNotPushed(NotificationJob::class, function ($job) {
            return $job->channel->getCode() === 'email';
        });
    }
}
