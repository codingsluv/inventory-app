<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $fillable = ['product_id', 'qty', 'reason'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
