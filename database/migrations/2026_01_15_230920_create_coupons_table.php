<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date');
            $table->integer('usage_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
