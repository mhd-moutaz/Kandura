<?php

namespace App\Http\Controllers\Users;

use App\Http\Resources\Users\OrderResource;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Services\Users\OrderService;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function store(StoreOrderRequest $request){
        $validated = $request->validated();
        $order = $this->orderService->createOrder($validated);
        return $this->success(new OrderResource($order), 'Order created successfully');
    }
}
