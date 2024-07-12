<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailChannel = Channel::where('code', 'email')->first();
        $smsChannel = Channel::where('code', 'sms')->first();
        $pushChannel = Channel::where('code', 'push')->first();

        Provider::create([
            'code' => 'aws-ses',
            'name' => 'AWS SES',
            'is_enabled' => true,
            'channel_id' => $emailChannel->id
        ]);

        Provider::create([
            'code' => 'twilio',
            'name' => 'Twilio',
            'is_enabled' => true,
            'channel_id' => $smsChannel->id
        ]);

        Provider::create([
            'code' => 'pushy',
            'name' => 'Pushy',
            'is_enabled' => true,
            'channel_id' => $pushChannel->id
        ]);
    }
}
