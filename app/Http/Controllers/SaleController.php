<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'array',
            'total' => 'numeric',
            'tendered_amount' => 'required|numeric|gte:total',
            'change' => 'required|numeric',
            'client_name' => 'required|string',
            'processed_by' => 'required|string',
        ]);

        foreach($validated['items'] as $item) 
        {
            $stock = intval($item['stock']) - intval($item['quantity']);
            
            if($stock < 0) {
                return response('Out of Stock');
            }

            Product::query()
                ->where('id', $item['id'])
                ->update(['stock' => $stock]);
        }

        $items = collect([])->values();
        
        $items->push(['items' => $validated['items']]);
        

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
}
