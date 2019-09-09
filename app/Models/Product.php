<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    protected $guarded = ['id'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale is a bool type file
    ];
    //connect with skus
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
