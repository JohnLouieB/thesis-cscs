<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Report/Index');
    }

    public function getSalesReport(Request $request) 
    {
        $today = Carbon::today()->toDateString();

        $formattedDate = null;
        
        if($request->searchByDate) {
            $formattedDate = Carbon::parse($request->searchByDate)->format('Y-m-d');
        }

        $data = Sale::query()
            ->whereNotNull('items')
            ->when(request('searchByClient'), function ($query) use ($request, $today) {
                $query->where('client_name', 'LIKE', '%'.$request->searchByClient.'%')
                ->orWhere('processed_by', 'LIKE', '%'.$request->searchByClient.'%');
            })
            ->when($request->searchByDate == null, function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->when(request('searchByDate'), function ($query) use ($request, $formattedDate) {
                $query->whereDate('created_at', $formattedDate);
            })
            ->latest()
            ->get();
        
        $sales = $data->map(function ($item) {
            $item->items = json_decode($item->items, true);
            return $item;
        });
        return response($sales);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
