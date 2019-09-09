<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //

    protected $guarded=['id'];
    protected $dates = ['last_used_at'];// this is to make sure $address->last_used_at will return a date.


    public function user(){

        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}{$this->city}{$this->state}{$this->post_code}";
    }
}
