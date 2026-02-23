<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Order $order): bool
    {
        // User can view invoice for their own order
        return $user->id === $order->user_id;
    }

}
