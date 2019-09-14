<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\ProductSku;
use Carbon\Carbon;





class OrdersController extends Controller
{

//    public function store(OrderRequest $request, OrderService $orderService)
//    {
//        $user    = $request->user();
//        $address = UserAddress::find($request->input('address_id'));
//
//        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
//    }

    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            //
            $address->update(['last_used_at' => Carbon::now()]);
            $order   = new Order([
                'address'      => [
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
            }

            $order->update(['total_amount' => $totalAmount]);

            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        return $order;
    }



    public function index(Request $request)
    {
        $orders = Order::query()
            // use with function to avoid the n+1 problem
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }
}
