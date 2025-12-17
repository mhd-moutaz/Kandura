<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreOrderItemRequest;
use App\Http\Requests\Users\UpdateOrderItemRequest;
use App\Http\Services\Users\OrderItemsService;
use App\Http\Resources\Users\OrderItemResource;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Gate;

class OrderItemsController extends Controller
{
    protected $orderItemsService;

    public function __construct(OrderItemsService $orderItemsService)
    {
        $this->orderItemsService = $orderItemsService;
    }

    /**
     * إضافة item جديد للطلب
     */
    public function store(StoreOrderItemRequest $request)
    {
        $orderItem = $this->orderItemsService->store($request->validated());
        return $this->success(
            new OrderItemResource($orderItem),
            'Order item added successfully',
            201
        );
    }

    /**
     * تحديث order item
     */
    public function update(UpdateOrderItemRequest $request, OrderItems $orderItem)
    {
        Gate::authorize('update', $orderItem);

        $updatedItem = $this->orderItemsService->update($orderItem, $request->validated());
        return $this->success(
            new OrderItemResource($updatedItem),
            'Order item updated successfully'
        );
    }

    /**
     * حذف order item
     */
    public function destroy(OrderItems $orderItem)
    {
        Gate::authorize('delete', $orderItem);

        $result = $this->orderItemsService->destroy($orderItem);
        return $this->success($result, $result['message']);
    }
}
