<?php

namespace App\Http\Controllers\Admins;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Admins\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'payment_method', 'sort_dir']);
        $orders = $this->orderService->index($filters);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->orderService->show($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,completed,cancelled'
        ]);

        $this->orderService->updateStatus($order, $validated['status']);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
