<?php

namespace App\Http\Controllers;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;


use Illuminate\Http\Request;

use App\Services\CartService;


class CartController extends Controller
{

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    //
    public function add(AddCartRequest $request)
    {
//        $user   = $request->user();
//        $skuId  = $request->input('sku_id');
//        $amount = $request->input('amount');
//
//        //to double check if the item has already in the cart
//        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
//
//            // if it is already in the cart add the amount to the record
//            $cart->update([
//                'amount' => $cart->amount + $amount,
//            ]);
//        } else {
//
//            //if not, just create a new record
//            $cart = new CartItem(['amount' => $amount]);
//            $cart->user()->associate($user);
//            $cart->productSku()->associate($skuId);
//            $cart->save();
//        }

        $this->cartService->add($request->input('sku_id'), $request->input('amount'));


        return [];
    }

    public function index(Request $request)
    {
//        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
//        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }




    public function remove(ProductSku $sku, Request $request)
    {
//        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        $this->cartService->remove($sku->id);

        return [];
    }
}
