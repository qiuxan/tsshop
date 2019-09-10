@extends('layouts.app')
@section('title', '我的收藏')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">My Favor</div>
                <div class="card-body">
                    <div class="row products-list">
                        @foreach($products as $product)
                            <div class="col-3 product-item">
                                <div class="product-content">
                                    <div class="top">
                                        <div class="img">
                                            <a href="{{ route('products.show', ['product' => $product->id]) }}">
                                                <img src="{{ $product->image_url }}" alt="">
                                            </a>
                                        </div>
                                        <div class="price"><b>￥</b>{{ $product->price }}</div>
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
                                    </div>
                                    <div class="bottom">
                                        <div class="sold_count">Sales <span>{{ $product->sold_count }} Orders</span></div>
                                        <div class="review_count">Review <span>{{ $product->review_count }}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="float-right">{{ $products->render() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection