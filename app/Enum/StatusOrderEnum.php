<?php

namespace App\Enum;

enum StatusOrderEnum
{
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const PROCESSING = 'processing';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
}
