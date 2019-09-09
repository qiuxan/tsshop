@extends('layouts.app')
@section('title', ($address->id?'Edit ':'Add').'Shipping Address')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">
                        {{$address->id?'Edit ':'Add'}} Shipping Address
                    </h2>
                </div>
                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <h4>Error：</h4>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($address->id)
                    <form class="form-horizontal" role="form" action="{{ route('user_addresses.update', ['user_address' => $address->id]) }}" method="post">
                    @method('PUT')
                    @else
                    <form class="form-horizontal" role="form" action="{{ route('user_addresses.store') }}" method="post">
                    @endif
                        @csrf


                        <div class="form-row">
                                <label class="col-form-label col-sm-2 text-md-right"><b>State and City</b></label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="state">
                                        <option value="" disabled selected>Choose Your State</option>
                                        <option value="vic" {{old('state',$address->state)=='vic'?'selected':''}}>Victoria</option>
                                        <option value="nsw" {{old('state',$address->state)=='nsw'?'selected':''}}>New South Wales</option>
                                        <option value="sa" {{old('state',$address->state)=='sa'?'selected':''}}>South Australia</option>
                                        <option value="tas" {{old('state',$address->state)=='tas'?'selected':''}}>Tasmania</option>
                                        <option value="nt" {{old('state',$address->state)=='nt'?'selected':''}}>Northern Territory</option>
                                        <option value="act" {{old('state',$address->state)=='act'?'selected':''}}>Australian Capital Territory</option>
                                    </select>
                                </div>
                                <div class="col-sm-3"><input class="form-control" type="text" name="city" placeholder="Enter Your City" value="{{ old('city', $address->city) }}"></div>
                                <div class="col-sm-3"><input class="form-control" type="text" name="post_code" placeholder="Enter Your Post Code" value="{{ old('post_code', $address->post_code) }}"></div>
                            </div>

                        <div class="form-group row mt-3">
                            <label class="col-form-label text-md-right col-sm-2"><b>Address</b></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-form-label text-md-right col-sm-2"><b>Name</b></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <label class="col-form-label text-md-right col-sm-2"><b>Phone</b></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
                            </div>
                        </div>

                        <div class="form-group row text-center">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection