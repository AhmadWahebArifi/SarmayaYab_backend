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
        'note',
        'reviewed_by',
        'reviewed_at',
        'dispatched_at',
        'delivered_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'dispatched_at' => 'datetime',
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
