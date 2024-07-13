<?php

namespace Database\Factories;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender' => $this->faker->name(),
            'recipient' => $this->faker->name(),
            'channel_id' => $this->faker->uuid(),
            'status' => NotificationStatus::PENDING
        ];
    }
}
