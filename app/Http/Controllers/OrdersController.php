<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        //start an db
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            // update the last used date
            $address->update(['last_used_at' => Carbon::now()]);
            // create an order
            $order   = new Order([
                'address'      => [ // put info. into this order
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            //link this order to the logged in user
            $order->user()->associate($user);
            //saved in db
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');
            // find the skus of this order
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // create an order item to connect with this order
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('Not enough stock');
                }
            }

            //update the total amount of this order
            $order->update(['total_amount' => $totalAmount]);

            // remove the items from the chart
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        return $order;
    }
}