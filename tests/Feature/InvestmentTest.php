<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Investment;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting investments for authenticated user.
     */
    public function test_user_can_get_investments(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        Investment::factory(3)->create(['user_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/investments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Test creating a new investment.
     */
    public function test_user_can_create_investment(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $investmentData = [
            'title' => 'Test Investment',
            'description' => 'Test description',
            'amount' => 500,
            'expected_return' => 600,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'type' => 'stocks',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/investments', $investmentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'amount',
                'expected_return',
                'actual_return',
                'start_date',
                'end_date',
                'status',
                'type',
                'user_id',
                'created_at',
                'updated_at',
                'transactions',
            ]);

        $this->assertDatabaseHas('investments', [
            'title' => 'Test Investment',
            'user_id' => $user->id,
        ]);

        $this->assertEquals(500, $user->fresh()->balance);
    }

    /**
     * Test creating investment with insufficient balance.
     */
    public function test_user_cannot_create_investment_with_insufficient_balance(): void
    {
        $user = User::factory()->create(['balance' => 100]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $investmentData = [
            'title' => 'Test Investment',
            'amount' => 500,
            'expected_return' => 600,
            'start_date' => '2024-01-01',
            'type' => 'stocks',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/investments', $investmentData);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Insufficient balance',
            ]);
    }

    /**
     * Test getting investment summary.
     */
    public function test_user_can_get_investment_summary(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        Investment::factory(3)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'actual_return' => 1200,
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/investments/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_invested',
                'total_returns',
                'net_profit',
                'active_investments',
                'completed_investments',
                'total_investments',
            ]);
    }
}
