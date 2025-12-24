<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // تغيير نوع العمود من integer إلى decimal
            $table->decimal('Langitude', 10, 8)->nullable()->change();
            $table->decimal('Latitude', 11, 8)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // العودة إلى النوع الأصلي في حالة التراجع
            $table->integer('Langitude')->nullable()->change();
            $table->integer('Latitude')->nullable()->change();
        });
    }
};
