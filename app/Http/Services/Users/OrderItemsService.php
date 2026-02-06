<?php

namespace App\Http\Services\Users;

use App\Exceptions\GeneralException;
use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderItemsService
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            // 1. التحقق من وجود التصميم
            $design = Design::lockForUpdate()->find($data['design_id']);
            if (!$design) {
                throw new GeneralException('Design not found.', 404);
            }
            if($design->state == false){
                throw new GeneralException('This design is inactive and cannot be ordered.', 400);
            }
            if (!$design->designOptions()->exists()) {
                throw new GeneralException('This design has no design options.', 400);
            }

            // 2. التحقق من الكمية المتوفرة
            if ($design->quantity <= 0) {
                throw new GeneralException('This design is currently out of stock.', 400);
            }

            // 3. حساب الكمية المطلوبة (العناصر الموجودة في السلة + الكمية المطلوبة)
            $requestedQuantity = $data['quantity'];
            $existingOrderItems = OrderItems::where('design_id', $design->id)
                ->whereHas('order', function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('status', 'pending');
                })
                ->sum('quantity');

            $totalNeeded = $existingOrderItems + $requestedQuantity;

            if ($totalNeeded > $design->quantity) {
                throw new GeneralException(
                    "Insufficient stock. Available: {$design->quantity}, In cart: {$existingOrderItems}, Requested: {$requestedQuantity}",
                    400
                );
            }
            // 2. البحث عن المقاس
            $measurement = Measurement::find($data['measurement_id']);
            if (!$measurement) {
                throw new GeneralException('Invalid measurement provided.', 400);
            }

            // 3. التحقق من أن المقاس موجود في التصميم
            if (!$design->measurements()->where('measurements.id', $measurement->id)->exists()) {
                throw new GeneralException('This measurement is not available for this design.', 400);
            }

            // 4. حساب السعر تلقائياً
            $quantity = $data['quantity'];
            $unitPrice = $design->price;
            $totalPrice = $unitPrice * $quantity;

            // 5. البحث عن طلب معلق أو إنشاء طلب جديد
            $order = Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$order) {
                $defaultAddress = $user->addresses()->oldest()->first();
                if (!$defaultAddress) {
                    throw new GeneralException('No address found for user. Please add an address first.', 400);
                }

                $orderData = [
                    'address_id' => $defaultAddress->id,
                ];
                $order = $this->orderService->createOrder($orderData);
            }

            // 6. إنشاء عنصر الطلب
            $orderItem = $order->orderItems()->create([
                'design_id' => $design->id,
                'measurement_id' => $measurement->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            // 7. ربط خيارات التصميم
            if (isset($data['design_options']) && is_array($data['design_options'])) {
                $designOptionIds = [];
                foreach ($data['design_options'] as $optionData) {
                    $designOption = DesignOption::where('type', $optionData['type'])
                        ->where(function ($query) use ($optionData) {
                            if (isset($optionData['name']['en'])) {
                                $query->orWhere('name->en', $optionData['name']['en']);
                            }
                            if (isset($optionData['name']['ar'])) {
                                $query->orWhere('name->ar', $optionData['name']['ar']);
                            }
                        })
                        ->first();
                    if (!$designOption) {
                        throw new GeneralException('Design option not found.', 404);
                    }
                    $existsInDesign = $design->designOptions()
                        ->where('design_options.id', $designOption->id)
                        ->exists();

                    if (!$existsInDesign) {
                        throw new GeneralException(
                            'This design option is not available for the selected design.',
                            400
                        );
                    }
                    $designOptionIds[] = $designOption->id;
                }

                if (!empty($designOptionIds)) {
                    $orderItem->designOptions()->sync($designOptionIds);
                }
            }

            // 8. تحديث إجمالي الطلب
            $this->updateOrderTotal($order);

            return $orderItem->load('design', 'measurement', 'designOptions');
        });
    }

    /**
     * تحديث order item
     */
    public function update(OrderItems $orderItem, $data)
    {
        return DB::transaction(function () use ($orderItem, $data) {
            $user = Auth::user();
            $order = $orderItem->order;


            // التحقق من أن الطلب يخص المستخدم
            if ($order->user_id !== $user->id) {
                throw new GeneralException('Unauthorized action.', 403);
            }

            // التحقق من أن الطلب لا يزال معلقاً
            if ($order->status !== 'pending') {
                throw new GeneralException('Cannot update items in a non-pending order.', 400);
            }

            $design = $orderItem->design;

            // إذا تم تغيير المقاس
            if (isset($data['measurement_id'])) {
                $measurement = Measurement::find($data['measurement_id']);
                if (!$measurement) {
                    throw new GeneralException('Invalid measurement provided.', 400);
                }

                if (!$design->measurements()->where('measurements.id', $measurement->id)->exists()) {
                    throw new GeneralException('This measurement is not available for this design.', 400);
                }

                $orderItem->measurement_id = $measurement->id;
            }

            // إذا تم تغيير الكمية
            if (isset($data['quantity'])) {
                $quantityDifference = $data['quantity'] - $orderItem->quantity;

                // إذا كانت الكمية تزيد، نتحقق من توفر المخزون
                if ($quantityDifference > 0) {
                    // قفل التصميم للتحديث المتزامن
                    $designLocked = Design::lockForUpdate()->find($orderItem->design_id);

                    // حساب الكمية الموجودة في السلة (باستثناء العنصر الحالي)
                    $existingOrderItems = OrderItems::where('design_id', $designLocked->id)
                        ->where('id', '!=', $orderItem->id)
                        ->whereHas('order', function($q) use ($user) {
                            $q->where('user_id', $user->id)
                              ->where('status', 'pending');
                        })
                        ->sum('quantity');

                    $totalNeeded = $existingOrderItems + $data['quantity'];

                    if ($totalNeeded > $designLocked->quantity) {
                        throw new GeneralException(
                            "Insufficient stock. Available: {$designLocked->quantity}, In cart: {$existingOrderItems}, Requested: {$data['quantity']}",
                            400
                        );
                    }
                }

                $quantity = $data['quantity'];
                $unitPrice = $design->price;
                $totalPrice = $unitPrice * $quantity;

                $orderItem->quantity = $quantity;
                $orderItem->unit_price = $unitPrice;
                $orderItem->total_price = $totalPrice;
            }

            $orderItem->save();

            // تحديث خيارات التصميم إن وجدت
            if (isset($data['design_options']) && is_array($data['design_options'])) {
                $designOptionIds = [];

                foreach ($data['design_options'] as $optionData) {
                    $designOption = DesignOption::where('type', $optionData['type'])
                        ->where(function ($query) use ($optionData) {
                            if (isset($optionData['name']['en'])) {
                                $query->orWhere('name->en', $optionData['name']['en']);
                            }
                            if (isset($optionData['name']['ar'])) {
                                $query->orWhere('name->ar', $optionData['name']['ar']);
                            }
                        })
                        ->first();

                    if ($designOption) {

                        // ✅ التحقق أن الخيار تابع للتصميم
                        if (
                            !$design->designOptions()
                                ->where('design_options.id', $designOption->id)
                                ->exists()
                        ) {
                            throw new GeneralException(
                                'This design option is not available for the selected design.',
                                400
                            );
                        }

                        $designOptionIds[] = $designOption->id;
                    }
                }

                if (!empty($designOptionIds)) {
                    $orderItem->designOptions()->sync($designOptionIds);
                }
            }


            // تحديث إجمالي الطلب
            $this->updateOrderTotal($order);

            return $orderItem->fresh()->load('design', 'measurement', 'designOptions');
        });
    }

    /**
     * حذف order item
     */
    public function destroy(OrderItems $orderItem)
    {
        return DB::transaction(function () use ($orderItem) {
            $order = $orderItem->order;


            // التحقق من أن الطلب لا يزال معلقاً
            if ($order->status !== 'pending') {
                throw new GeneralException('Cannot delete items from a non-pending order.', 400);
            }

            // حذف العلاقات مع design options
            $orderItem->designOptions()->detach();

            // حذف order item
            $orderItem->delete();

            // التحقق من وجود items أخرى في الطلب
            $remainingItems = $order->orderItems()->count();

            if ($remainingItems === 0) {
                // حذف الطلب إذا لم يعد يحتوي على items
                $order->delete();
                return [
                    'order_deleted' => true,
                    'message' => 'Order item deleted and order removed as it had no remaining items.'
                ];
            } else {
                // تحديث إجمالي الطلب
                $this->updateOrderTotal($order);
                return [
                    'order_deleted' => false,
                    'remaining_items' => $remainingItems,
                    'message' => 'Order item deleted successfully.'
                ];
            }
        });
    }

    /**
     * تحديث إجمالي سعر الطلب
     */
    private function updateOrderTotal(Order $order)
    {
        $total = $order->orderItems()->sum('total_price');
        $order->update(['total' => $total]);
    }
}
