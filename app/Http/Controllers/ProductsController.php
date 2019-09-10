<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function index(Request $request)
    {
        //create a checker to check db
        $builder = Product::query()->where('on_sale', true);

        //if there is search parameter set. if so $search will be set to search parameter
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            //search from  title, description, or sku's title and description
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        //if there is order parameter set. if so $order will be set to order parameter
        if ($order = $request->input('order', '')) {
            // check if the parameter is ending with_asc or _desc
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }
        $products = $builder->paginate(16);

        return view('products.index', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }


    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new \Exception('Product is not on sale!');
        }

        return view('products.show', ['product' => $product]);
    }

}
