<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'branch_id',
        'created_by',
        'status',
        'priority',
        'expected_delivery_date',
        'note',
        'reason',
        'total_value',
        'cost_center',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'approval_notes',
        'dispatched_at',
        'tracking_number',
        'estimated_delivery',
        'delivered_at',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'total_value' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'estimated_delivery' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }
}
