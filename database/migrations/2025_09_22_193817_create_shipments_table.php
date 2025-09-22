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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('carrier', ['inpost'])->default('inpost');
            $table->enum('service_type', ['parcel_locker', 'courier'])->default('parcel_locker');
            $table->string('tracking_number')->nullable();
            $table->string('label_path')->nullable();
            $table->enum('status', ['created', 'in_transit', 'delivered', 'returned', 'lost'])->default('created');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
