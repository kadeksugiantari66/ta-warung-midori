<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->withCount('orderItems')
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'image'        => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_available'] = $request->boolean('is_available', true);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'reviews', 'orderItems']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'image'        => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_available'] = $request->boolean('is_available');

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Mengubah status ketersediaan menu (dimatikan kalau bahannya lagi habis).
     */
    public function toggleAvailability(Product $product): RedirectResponse
    {
        $product->update(['is_available' => !$product->is_available]);
        $status = $product->is_available ? 'tersedia' : 'habis';

        return back()->with('success', "{$product->name} ditandai sebagai {$status}.");
    }

    /**
     * Mengambil ranking makanan terpopuler dari pesanan yang masuk.
     */
    public function popular(): View
    {
        $products = Product::with('category')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        return view('admin.products.popular', compact('products'));
    }
}
