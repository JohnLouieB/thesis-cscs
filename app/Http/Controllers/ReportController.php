<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');  

        return Inertia::render('Report/Index');
    }

    public function getSalesReport(Request $request) 
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');  

        $formattedDate = null;
        $year = null;
        $month = null;
        $formattedStartWeek = null;
        $formattedEndWeek = null;
        $returnDate = null;
        
        if($request->generate && $request->generateReport == 'daily') {
            $formattedDate = Carbon::parse($request->generate)->format('Y-m-d')  ;
            $returnDate = Carbon::parse($request->generate)->toFormattedDateString();
        }

        if($request->generate && $request->generateReport == 'weekly') {
            $formattedStartWeek = Carbon::parse($request->generate[0])->format('Y-m-d') . ' 00:00:00';
            $formattedEndWeek = Carbon::parse($request->generate[1])->format('Y-m-d') . ' 23:59:59';
            $returnDate = Carbon::parse($request->generate[0])->toFormattedDateString() . ' to ' . Carbon::parse($request->generate[1])->toFormattedDateString();
        }

        if($request->generate && $request->generateReport == 'monthly') {
            $formattedMonth = Carbon::parse($request->generate)->format('Y-m-d');
            $temp = explode("-", $formattedMonth);
            $year = $temp[0];
            $month = $temp[1];
            $tempDate = Carbon::parse($request->generate)->toFormattedDateString();
            $date = explode(" " , $tempDate);
            $returnDate = $date[0] .' '. $date[2];
        }

        $data = Sale::query()
            ->whereNotNull('items')
            ->when(request('searchByClient'), function ($query) use ($request) {
                $query->where('client_name', 'LIKE', '%'.$request->searchByClient.'%')
                ->orWhere('processed_by', 'LIKE', '%'.$request->searchByClient.'%');
            })
            ->when(request('generate') && $request->generateReport == 'daily', function ($query) use ($formattedDate) {
                $query->whereDate('created_at', $formattedDate);
            })
            ->when(request('generate') && $request->generateReport == 'weekly', function ($query) use ($formattedStartWeek, $formattedEndWeek) {
                $query->whereBetween('created_at', [$formattedStartWeek, $formattedEndWeek]);
            })
            ->when(request('generate') && $request->generateReport == 'monthly', function ($query) use ($year, $month) {
                $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
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

        return response()->json([
            'report' => $sales,
            'date' => $returnDate
        ]);
    }
}
