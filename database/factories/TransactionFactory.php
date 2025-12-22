<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Investment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['deposit', 'withdrawal', 'investment', 'return']);
        
        return [
            'user_id' => User::factory(),
            'investment_id' => in_array($type, ['investment', 'return']) 
                ? Investment::factory() 
                : null,
            'amount' => fake()->randomFloat(2, 10, 10000),
            'type' => $type,
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'metadata' => fake()->optional() ? [
                'payment_method' => fake()->randomElement(['bank_transfer', 'credit_card', 'cash']),
                'reference' => fake()->uuid(),
                'fees' => fake()->randomFloat(2, 0, 50),
            ] : null,
        ];
    }
}
