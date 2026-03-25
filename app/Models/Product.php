<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'supplier',
        'purchase_price',
        'selling_price',
        'reorder_point',
        'active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function warehouseInventory()
    {
        return $this->hasOne(WarehouseInventory::class);
    }

    public function branchInventories()
    {
        return $this->hasMany(BranchInventory::class);
    }

    public function stockRequestItems()
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
