<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\ProductSku;
use Carbon\Carbon;

use App\Http\Requests\SendReviewRequest;





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


    public function review(Order $order)
    {
//        return $order;
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        });

        return redirect()->back();
    }
}
