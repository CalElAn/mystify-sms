<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GradingScale>
 */
class GradingScaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'scale' => json_encode([
                'A+' => [90, 100],
                'A' => [85, 89],
                'B+' => [80, 84],
                'B' => [75, 79],
                'C' => [70, 74],
                'D' => [60, 69],
                'E' => [50, 59],
                'F' => [0, 49],
            ])
        ];
    }
}
