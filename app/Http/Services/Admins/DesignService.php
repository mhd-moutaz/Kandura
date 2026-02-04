<?php
namespace App\Http\Services\Admins;

use App\Models\Design;
use App\Models\DesignOption;
use App\Models\OrderItems;
use App\Exceptions\GeneralException;
use Illuminate\Http\Request;


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
}
