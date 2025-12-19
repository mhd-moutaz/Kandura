<?php

namespace App\Http\Controllers\Users;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmOrderRequest;
use App\Http\Services\Users\OrderService;
use App\Http\Resources\Users\OrderResource;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index()
    {
        $orders = $this->orderService->getUserOrders();
        return $this->success(
            OrderResource::collection($orders),
            'User orders retrieved successfully'
        );
    }
    public function confirmOrder(Order $order,ConfirmOrderRequest $request)
    {
        Gate::authorize('update', $order);
        $order = $this->orderService->confirmOrder($order, $request->validated());
        return $this->success(new OrderResource($order), 'Order confirmed successfully');
    }
}
