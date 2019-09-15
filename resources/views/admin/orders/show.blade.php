<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Order Number：{{ $order->no }}</h3>
        <div class="box-tools">
            <div class="btn-group float-right" style="margin-right: 10px">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td>Buyer：</td>
                <td>{{ $order->user->name }}</td>
                <td>Payment Time：</td>
                <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td>Payment Method：</td>
                <td>{{ $order->payment_method }}</td>
                <td>Payment ID：</td>
                <td>{{ $order->payment_no }}</td>
            </tr>
            <tr>
                <td>Shipping Address</td>
                <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
            </tr>
            <tr>
                <td rowspan="{{ $order->items->count() + 1 }}">Item List</td>
                <td>Item Title</td>
                <td>Price </td>
                <td>Quantity</td>
            </tr>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
                    <td>${{ $item->price }}</td>
                    <td>{{ $item->amount }}</td>
                </tr>
            @endforeach
            <tr>
                <td>Order Amount：</td>
                <td>${{ $order->total_amount }}</td>
                <td>发货状态：</td>
                <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
            </tr>

            <!-- 订单发货开始 -->
            <!-- 如果订单未发货，展示发货表单 -->
            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
                <tr>
                    <td colspan="4">
                        <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">
                            <!-- 别忘了 csrf token 字段 -->
                            {{ csrf_field() }}
                            <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                                <label for="express_company" class="control-label">Courier: </label>
                                <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="Enter Courier">
                                @if($errors->has('express_company'))
                                    @foreach($errors->get('express_company') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                                <label for="express_no" class="control-label">Tracking Number</label>
                                <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="Enter Tracking number">
                                @if($errors->has('express_no'))
                                    @foreach($errors->get('express_no') as $msg)
                                        <span class="help-block">{{ $msg }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-success" id="ship-btn">发货</button>
                        </form>
                    </td>
                </tr>
            @else
                <!-- 否则展示物流公司和物流单号 -->
                <tr>
                    <td>Courier：</td>
                    <td>{{ $order->ship_data['express_company'] }}</td>
                    <td>Tracking Number：</td>
                    <td>{{ $order->ship_data['express_no'] }}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>