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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->onDelete('cascade'); // kalau order dihapus total (bukan soft), payment juga
            $table->enum('payment_method', ['COD', 'Transfer']);
            $table->enum('payment_status', ['Unpaid', 'Awaiting Approval', 'Paid', 'Rejected'])->default('Unpaid');
            $table->string('payment_proof', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
