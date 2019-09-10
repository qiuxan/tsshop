<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

class AddCartRequest extends Request
{
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('Product does not exist');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('Product is not on sale');
                    }
                    if ($sku->stock === 0) {
                        return $fail('Out of Stock');
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        return $fail('Not enough stock');
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'The amount of products'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => 'Please select an item'
        ];
    }
}