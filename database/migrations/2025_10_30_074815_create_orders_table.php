<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->uuid('token_order')->nullable()->unique();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->text('address');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['Pending', 'Processed', 'Shipped', 'Delivered', 'Cancelled'])->default('Pending');
            $table->timestamps();
            $table->softDeletes();
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
