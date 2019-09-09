<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //

            'state'      => 'required',
            'city'          => 'required',
            'post_code'      => 'required|numeric',
            'address'       => 'required',
            'contact_name'  => 'required',
            'contact_phone' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'state'      => 'State',
            'city'          => 'City',
            'post_code'      => 'Post Code',
            'address'       => 'Address',
            'contact_name'  => 'Contact Name',
            'contact_phone' => 'Phone Number'
        ];
    }
}
