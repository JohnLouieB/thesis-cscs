<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->whereNotNull('name')
            ->get();

        $categories = Category::all();

        $sales = Sale::query()
            ->whereNotNull('data')
            ->get();

        $date = [];

        foreach ($sales as $sale) {
            $date = Carbon::parse($sale->created_at)->toFormattedDateString();
        }

        $user = Auth::user()->name;

        return Inertia::render('Sales/Index', [
            'products' => $products,
            'sales' => $sales,
            'date' => $date,
            'user' => $user,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        foreach ($request->items as $item) {
            Log::debug($item['name']);
            $product = Product::query()
                ->where('name', $item['name'])
                ->firstOrFail();

            $test = $product->stock - $item['quantity'];
            $product->stock = $test;
            $product->save();
        };

        $data = [];
        // $products = json_encode($request->items);

        // // dd($request->items);
        foreach ($request->items as $name) {
            $data = ['name' => $name['name']];
        }

        Sale::create([
            'data' => json_encode($data),
            'total' => $request->total,
            'tendered_amount' => '₱' . $request->tendered_amount . '.00',
            'change' => '₱' . $request->change . '.00',
            'client_name' => $request->client_name,
            'processed_by' => $request->processed_by,
        ]);

        return Redirect::route('sales.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
