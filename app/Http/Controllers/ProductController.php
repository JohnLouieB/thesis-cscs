<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->whereNotNull('name')
            ->get();

        $categories = Category::query()
            ->whereNotNull('name')
            ->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        Product::create([
            'category' => Request::input('category'),
            'name' => Request::input('name'),
            'description' => Request::input('description'),
            'price' => Request::input('price'),
            'stock' => Request::input('stock'),
        ]);

        return Redirect::route('products.index');
    }

    public function update(Request $request, Product $product)
    {
        $product->update([
            'category' => Request::input('category'),
            'name' => Request::input('name'),
            'description' => Request::input('description'),
            'price' => Request::input('price'),
            'stock' => Request::input('stock'),
        ]);

        return Redirect::route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return Redirect::route('products.index');
    }

    public function getTopProducts()
    {
        $totalSales = Product::orderBy('total_sale', 'desc')->limit(5)->get();

        return response()->json($totalSales);
    }

    public function getProductCounts()
    {
        $data = Product::query()
            ->whereNotNull('name')
            ->get();

        return response()->json($data);
    }
}
