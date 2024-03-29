@extends('layouts.app')
@section('title', 'View Order')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <h4>Order Detail</h4>
                </div>
                <div class="card-body">


                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product Information</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-right item-amount">Item Amount</th>
                        </tr>
                        </thead>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="product-info">
                                    <div class="preview">
                                        <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                                            <img src="{{ $item->product->image_url }}">
                                        </a>
                                    </div>
                                    <div>
              <span class="product-title">
                 <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
              </span>
                                        <span class="sku-title">{{ $item->productSku->title }}</span>
                                    </div>
                                </td>
                                <td class="sku-price text-center vertical-middle">${{ $item->price }}</td>
                                <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                                <td class="item-amount text-right vertical-middle">${{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        <tr><td colspan="4"></td></tr>
                    </table>
                    <div class="order-bottom">
                        <div class="order-info">
                            <div class="line"><div class="line-label">Address：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
                            <div class="line"><div class="line-label">Note：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
                            <div class="line"><div class="line-label">OrderNumber: </div><div class="line-value">{{ $order->no }}</div></div>

                            <div class="line">
                                <div class="line-label">OrderStatus：</div>
                                <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
                            </div>
                            @if($order->ship_data)
                                <div class="line">
                                    <div class="line-label">Courier Info：</div>
                                    <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="order-summary text-right">
                            <div class="total-amount left">
                                <span>Amount：</span>
                                <div class="value">${{ $order->total_amount }}</div>
                            </div>
                            <div>
                                <span>Order Status：</span>
                                <div class="value">
                                    @if($order->paid_at)
                                        @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                           Paid
                                        @else
                                            {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                        @endif
                                    @elseif($order->closed)
                                        Close
                                    @else
                                       Unpaid
                                    @endif
                                </div>
                            </div>

                            {{--<div class="btn mr-5 mt-3" style="background-color: #00b5ad;">paypal button</div>--}}
                            @if(!$order->paid_at)

                                 <form action="{{route('order.pay')}}" method="post">
                                <input type="hidden" value="{{$order->total_amount}}" name="total_amount">

                                <input type="hidden" value="{{$order->address['address']}}" name="address">

                                <input type="hidden" value="{{$order->id}}" name="order_id">



                                <input type="hidden" value="{{$order->address['contact_phone']}}" name="contact_phone">


                                {{csrf_field()}}

                                <button role="button" class="mt-2 paypalbutton"  type="submit">

                                    <div class="paypal-btn-content mt-2 mr-"> <span class="">Pay with </span>
                                        <img src="/img/paypallogo.svg" alt="PayPal" class="" style=""></div>



                                </button>

                            </form>
                            @endif



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection