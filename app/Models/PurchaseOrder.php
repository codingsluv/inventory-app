<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['supplier_id', 'product_id', 'qty', 'total_price', 'purchase_date'];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    protected static function booted()
    {
        static::created(function ($purchaseOrder) {
            $product = $purchaseOrder->product;
                if ($product) {
                    $product->stock -= $purchaseOrder->quantity;
                    $product->save();
            }
        });
    }
}


