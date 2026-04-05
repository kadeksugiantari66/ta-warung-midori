<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Pesanan yang sudah dibayar dan perlu disiapkan dapur
        $activeOrders = Order::with(['table', 'orderItems.product'])
            ->whereIn('status', ['confirmed', 'processing', 'ready'])
            ->latest()
            ->get();

        $pollHash = Order::whereIn('status', ['confirmed', 'processing', 'ready'])->max('updated_at');

        return view('dapur.dashboard', compact('activeOrders', 'pollHash'));
    }

    public function poll(): \Illuminate\Http\JsonResponse
    {
        $hash = Order::whereIn('status', ['confirmed', 'processing', 'ready'])->max('updated_at');
        return response()->json(['hash' => $hash]);
    }
}
