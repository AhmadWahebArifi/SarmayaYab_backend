<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users with related investments and transactions
        User::factory()
            ->count(10)
            ->has(Investment::factory()->count(2))
            ->has(Transaction::factory()->count(5))
            ->create();
    }
}
