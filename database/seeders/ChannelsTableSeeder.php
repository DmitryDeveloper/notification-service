<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Channel::create([
            'code' => 'email',
            'name' => 'Email Channel',
            'is_enabled' => true,
        ]);

        Channel::create([
            'code' => 'sms',
            'name' => 'SMS Channel',
            'is_enabled' => true,
        ]);

        Channel::create([
            'code' => 'push',
            'name' => 'Push Notification Channel',
            'is_enabled' => true,
        ]);
    }
}
