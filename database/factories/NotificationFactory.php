<?php

namespace Database\Factories;

use App\Models\User;
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
            'type' => 'App\Notifications\JoinSchoolRequest', 
            'notifiable_type' => 'App\Models\School',
            'notifiable_id' => 1,
            'data' => json_encode(['user' => User::all()->random()]),
        ];

    }
}
