<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        $featured = Product::active()
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featured'));
    }
}
