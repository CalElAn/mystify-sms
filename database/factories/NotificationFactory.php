<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'type' => 'App\Notifications\CallBackRequested', 
            // 'notifiable_type' => 'App\Models\User',
            // 'notifiable_id' => 2,
            // 'data' => json_encode(['message' => $this->faker->paragraphs(5, true)]),
        ];
    }
}
