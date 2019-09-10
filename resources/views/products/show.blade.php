@extends('layouts.app')
@section('title', $product->title)

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body product-info">
                    <div class="row">
                        <div class="col-5">
                            <img class="cover" src="{{ $product->image_url }}" alt="">
                        </div>
                        <div class="col-7">
                            <div class="title">{{ $product->title }}</div>
                            <div class="price"><label>Price: </label><em>$</em><span>{{ $product->price }}</span></div>
                            <div class="sales_and_reviews">
                                <div class="sold_count">Sold: </div>
                                <div class="review_count">Review: <span class="count">{{ $product->review_count }}</span></div>
                                <div class="rating" title="stars {{ $product->rating }}">Stars <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
                            </div>
                            <div class="skus">
                                <label>Option</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">


                                    @foreach($product->skus as $sku)
                                        <label
                                                class="btn sku-btn"
                                                data-price="{{ $sku->price }}"
                                                data-stock="{{ $sku->stock }}"
                                                data-toggle="tooltip"
                                                title="{{ $sku->description }}"
                                                data-placement="bottom">
                                            <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
                                        </label>
                                    @endforeach

                                </div>
                            </div>
                            <div class="cart_amount"><label>Quantity</label><input type="text" class="form-control form-control-sm" value="1"><span>pcs</span><span class="stock"></span></div>
                            <div class="buttons">

                                @if($favored)
                                    <button class="btn btn-danger btn-disfavor">Unfavor</button>
                                @else
                                    <button class="btn btn-success btn-favor">❤ Favor</button>
                                @endif




                                <button class="btn btn-primary btn-add-to-cart">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                    <div class="product-detail">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">Product Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">User Feedback</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                                {!! $product->description !!}
                            </div>
                            <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
            $('.sku-btn').click(function () {
                $('.product-info .price span').text($(this).data('price'));
                $('.product-info .stock').text('Storage：' + $(this).data('stock') + ' pcs');
            });
            $('.btn-favor').click(function () {
                axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
                    .then(function () {
                        swal('Success', '', 'success')
                            .then(function () {
                                location.reload();
                            });
                    }, function(error) {
                        if (error.response && error.response.status === 401) {
                            swal('Please Login First', '', 'error');
                        }  else if (error.response && error.response.data.msg) {
                            swal(error.response.data.msg, '', 'error');
                        }  else {
                            swal('Internal Error', '', 'error');
                        }
                    });
            });

            $('.btn-disfavor').click(function () {
                axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
                    .then(function () {
                        swal('Success', '', 'success')
                            .then(function () {
                                location.reload();
                            });
                    });
            });


            //click add button
            $('.btn-add-to-cart').click(function () {

                //request the add api
                axios.post('{{ route('cart.add') }}', {
                    sku_id: $('label.active input[name=skus]').val(),
                    amount: $('.cart_amount input').val(),
                })
                    .then(function () { //success
                        swal('Item added', '', 'success')
                            .then(function () {
                                location.href = '{{ route('cart.index') }}';
                            });
                    }, function (error) { 
                        if (error.response.status === 401) {
//status 401 means user did not log in
                            swal('Login first', '', 'error');

                        } else if (error.response.status === 422) {

                            // http status 422 enter wrong validate
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error+'<br>';
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})
                        } else {

                            //
                            swal('system error', '', 'error');
                        }
                    })
            });

        });
    </script>
@endsection