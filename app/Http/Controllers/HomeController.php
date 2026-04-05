<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // 3 menu terpopuler untuk section Signature Dishes
        $featured = Product::with('category')
            ->withCount('orderItems')
            ->where('is_available', true)
            ->orderByDesc('order_items_count')
            ->take(3)
            ->get();

        return view('welcome', compact('featured'));
    }
}
