<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'investment_id',
        'amount',
        'type',
        'description',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the investment that owns the transaction.
     */
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * Check if the transaction is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transaction is a deposit.
     */
    public function isDeposit()
    {
        return $this->type === 'deposit';
    }

    /**
     * Check if the transaction is a withdrawal.
     */
    public function isWithdrawal()
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Check if the transaction is an investment.
     */
    public function isInvestment()
    {
        return $this->type === 'investment';
    }

    /**
     * Check if the transaction is a return.
     */
    public function isReturn()
    {
        return $this->type === 'return';
    }
}
