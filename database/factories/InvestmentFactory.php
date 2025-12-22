<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Investment>
 */
class InvestmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'expected_return' => fake()->randomFloat(2, 1000, 100000),
            'actual_return' => fake()->randomFloat(2, 0, 100000),
            'start_date' => fake()->date(),
            'end_date' => fake()->optional(0.7)->date(),
            'status' => fake()->randomElement(['active', 'completed', 'cancelled']),
            'type' => fake()->randomElement(['stocks', 'bonds', 'real_estate', 'crypto', 'other']),
            'metadata' => fake()->optional() ? [
                'risk_level' => fake()->randomElement(['low', 'medium', 'high']),
                'duration' => fake()->numberBetween(1, 60) . ' months',
                'country' => fake()->country(),
            ] : null,
        ];
    }
}