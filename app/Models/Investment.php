<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'expected_return',
        'actual_return',
        'start_date',
        'end_date',
        'status',
        'type',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expected_return' => 'decimal:2',
        'actual_return' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the investment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the investment.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate the return on investment percentage.
     */
    public function getRoiAttribute()
    {
        if ($this->amount == 0) {
            return 0;
        }
        
        return (($this->actual_return - $this->amount) / $this->amount) * 100;
    }

    /**
     * Check if the investment is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the investment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
