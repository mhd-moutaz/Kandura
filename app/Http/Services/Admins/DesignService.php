<?php
namespace App\Http\Services\Admins;

use App\Models\Design;
use App\Models\DesignOption;
use App\Models\OrderItems;
use App\Exceptions\GeneralException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DesignService{
    public function index($data)
    {
        $designOptions = DesignOption::all();
        $design = Design::filter($data)->paginate(9)->withQueryString(); // Return an array of designs for admin view
        return ['designs' => $design, 'designOptions' => $designOptions];
    }

    /**
     * Toggle design state (active/inactive)
     * When deactivating: check if design is not in any confirmed/processing orders
     */
    public function toggleState(Design $design): array
    {
        $currentState = $design->state;
        $newState = !$currentState;

        // If trying to deactivate (true -> false), check for active orders
        if ($currentState == true && $newState == false) {
            $hasActiveOrders = OrderItems::where('design_id', $design->id)
                ->whereHas('order', function ($query) {
                    $query->whereIn('status', ['confirmed', 'processing']);
                })
                ->exists();

            if ($hasActiveOrders) {
                throw new GeneralException(
                    'Cannot deactivate this design. It is currently in confirmed or processing orders.',
                    400
                );
            }
        }

        // Update state
        $design->update(['state' => $newState]);

        return [
            'success' => true,
            'new_state' => $newState,
            'message' => $newState
                ? 'Design activated successfully.'
                : 'Design deactivated successfully.'
        ];
    }

    /**
     * Update design quantity (increment or decrement)
     */
    public function updateQuantity(Design $design, array $data): array
    {
        DB::transaction(function() use ($design, $data) {
            $designLocked = Design::lockForUpdate()->find($design->id);
            $quantity = $data['quantity'];
            $action = $data['action'];

            switch($action) {
                case 'increment':
                    $designLocked->increment('quantity', $quantity);
                    break;
                case 'decrement':
                    if ($designLocked->quantity >= $quantity) {
                        $designLocked->decrement('quantity', $quantity);
                    } else {
                        throw new GeneralException(
                            'Cannot decrement below zero',
                            400
                        );
                    }
                    break;
            }
        });

        return [
            'success' => true,
            'message' => __('messages.quantity_updated')
        ];
    }
}
