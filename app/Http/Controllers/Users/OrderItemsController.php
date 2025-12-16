<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\OrderItemsService;
class OrderItemsController extends Controller
{
    protected $orderItemsService;
    public function __construct(OrderItemsService $orderItemsService)
    {
        $this->orderItemsService = $orderItemsService;
    }
    public function store(Request $request)  {
        $this->orderItemsService->store($request->validated());
    }
}
