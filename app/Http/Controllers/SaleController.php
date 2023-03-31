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
use Illuminate\Support\Arr;

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
            ->whereNotNull('total')
            ->get();

        $date = [];

        foreach ($sales as $sale) {
            $date = Carbon::parse($sale->created_at)->toFormattedDateString();
        }

        $user = Auth::user();

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

        $validated = $this->validateRequest($request);

        $items = collect([])->values();

        $items->push(['items' => $validated['items']]);

        // dd($items[0]);
        Sale::create([
            'items' => json_encode($validated['items']),
            'total' => $validated['total'],
            'tendered_amount' => '₱' . $validated['tendered_amount'] . '.00',
            'change' => '₱' . $validated['change'] . '.00',
            'client_name' => $validated['client_name'],
            'processed_by' => $validated['processed_by'],
        ]);

        return Redirect::route('sales.index');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'items' => 'array',
            'total' => 'numeric',
            'tendered_amount' => 'required|numeric',
            'change' => 'required|numeric',
            'client_name' => 'required|string',
            'processed_by' => 'required|string',
        ]);
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
