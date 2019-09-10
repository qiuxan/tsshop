<?php

namespace App\Http\Controllers;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;


use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function index(Request $request)
    {
        return view('user_addresses.index', [
            'addresses' => $request->user()->addresses,
        ]);
    }    //

    public function create()
    {
        return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);
    }

    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'state',
            'city',
            'post_code',
            'address',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    public function edit(UserAddress $user_address)
    {
        return view('user_addresses.create_and_edit', ['address' => $user_address]);
    }



    public function update(UserAddress $user_address, UserAddressRequest $request)
    {
        $user_address->update($request->only([
            'state',
            'city',
            'post_code',
            'address',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    public function destroy(UserAddress $user_address){
        $user_address->delete();
//        return redirect()->route('user_addresses.index');
        return [];
    }



}
