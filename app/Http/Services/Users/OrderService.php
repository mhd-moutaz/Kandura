<?php

namespace App\Http\Services\Users;

use App\Models\Order;
use App\Enum\StatusOrderEnum;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function createOrder($data){
        $data['user_id'] = Auth::id();
        $data['status'] = StatusOrderEnum::PENDING;
        $order = Order::create($data);
        return $order;
    }
}
