<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock',
        'low_stock_threshold',
        'status',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function updateStockStatus()
    {
        if ($this->stock == 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->stock <= $this->low_stock_threshold) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }

        $this->save();
    }
}
