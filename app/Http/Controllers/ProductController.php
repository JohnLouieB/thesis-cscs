<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
        // dd($request);
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:products,name,'.$request->id,
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'nullable',
        ]);

        Product::create([
            'category' => $validated['category'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        return Redirect::route('products.index');
    }

    public function update(Request $request, Product $product)
    {
        // dd($request);
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:products,name,'.$request->id,
            'description' => 'required|string|max:255',
            'price' => 'required',
            'stock' => 'nullable',
        ]);

        $product->update([
            'category' => $validated['category'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        return Redirect::route('products.index');
    }

    public function destroy(Product $product)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

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
