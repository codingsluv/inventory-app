<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = ['customer_id', 'product_id', 'qty', 'total_price', 'sale_date'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
