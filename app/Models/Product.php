<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'stock',
        'price',
        'category_id',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function stockout(){
        return $this->hasMany(Stockout::class);
    }

    public function stockins(){
        return $this->hasMany(StockIn::class);
    }

    // public function getTotalStockAttribute(){
    //     return $this->stock + $this->stockout()->sum('qty') - $this->stockins()->sum('qty');
    // }

    public function purchaseorders(){
        return $this->hasMany(PurchaseOrder::class);
    }

}
