<?php

namespace App\Http\Services\Users;

use App\Exceptions\GeneralException;
use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderItemsService
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function store($data)
    {
        $user = User::find(Auth::id());
        $order = Order::where('user_id', $user->id)->where('status', 'pending')->first();
        // $measurement = Measurement::where('size',$data['measurement_id'])->first();
        $design = Design::find($data['design_id']);
        if (!$design) {
            throw new GeneralException('Design not found.', 404);
        }
        $measurement = $design->measurements()->where('size', $data['measurement_id'])->first();
        if (!$measurement) {
            // fallback to global measurements table
            $measurement = Measurement::where('size', $data['measurement_id'])->first();
            if (!$measurement) {
                throw new GeneralException('Invalid measurement size provided.', 400);
            }
        }
        if (!$order) {
            $orderData = [
                'address_id' => $user->addresses()->oldest()->first()->id,
            ];
            $order = $this->orderService->createOrder($orderData);
        }
        $orderItem = $order->orderItems()->create([
            'design_id' => $data['design_id'],
            'measurement_id' => $data['measurement_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['total_price'],
        ]);
        if (isset($data['design_options'])) {
                $design_option_id = DesignOption::where('name',$data['design_options'])->first()->pluck('id');
                $orderItem->designOptions()->sync($design_option_id);
        }
        return $orderItem;
    }
}
