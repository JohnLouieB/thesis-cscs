<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->whereNotNull('name')
            ->get();

        return Inertia::render('Category/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        Category::create([
            'name' => Request::input('name'),
        ]);

        return Redirect::route('categories.index');
    }

    public function update(Category $categories)
    {
        $categories->update([
            'name' => Request::input('name'),
        ]);

        return Redirect::route('categories.index');
    }

    public function destroy(Category $category)
    {
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
