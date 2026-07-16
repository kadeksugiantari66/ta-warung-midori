<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Menampilkan daftar semua ulasan makanan dari para pelanggan.
     */
    public function index(): View
    {
        $reviews = Review::with('product')
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }
}
