<?php

namespace App\Http\Controllers;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function add(AddCartRequest $request)
    {
        $user   = $request->user();
        $skuId  = $request->input('sku_id');
        $amount = $request->input('amount');

        //to double check if the item has already in the cart
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {

            // if it is already in the cart add the amount to the record
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {

            //if not, just create a new record
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }
}
