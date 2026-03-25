<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region',
        'city',
        'address',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inventories()
    {
        return $this->hasMany(BranchInventory::class);
    }

    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class);
    }
}
