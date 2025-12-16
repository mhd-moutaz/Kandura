<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\StatusOrderEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
            $table->enum('status',[
                StatusOrderEnum::PENDING,
                StatusOrderEnum::CONFIRMED,
                StatusOrderEnum::PROCESSING,
                StatusOrderEnum::COMPLETED,
                StatusOrderEnum::CANCELLED,
            ])->default(StatusOrderEnum::PENDING);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'wallet'])->default('cash');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
