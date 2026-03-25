<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id',
        'product_id',
        'requested_qty',
        'approved_qty',
        'dispatched_qty',
    ];

    public function request()
    {
        return $this->belongsTo(StockRequest::class, 'stock_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
