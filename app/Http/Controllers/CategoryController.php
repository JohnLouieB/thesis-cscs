<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $categories = Category::query()
            ->whereNotNull('name')
            ->get();

        return Inertia::render('Category/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {   
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories'
        ]);

        Category::create([
            'name' => $validated['name'],
        ]);

        return Redirect::route('categories.index');
    }

    public function update(Request $request, Category $categories)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' .$request->id,
        ]);

        $categories->update([
            'name' => $validated['name'],
        ]);

        return Redirect::route('categories.index');
    }

    public function destroy(Category $category)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');
        
        $category->delete();

        return Redirect::route('categories.index');
    }

    public function getCategoryCounts()
    {
        $data = Category::query()
            ->whereNotNull('name')
            ->get();

        return response()->json($data);
    }
}
