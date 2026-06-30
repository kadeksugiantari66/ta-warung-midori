<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_order_item');
            $table->foreignId('id_order')->constrained('orders', 'id_order')->cascadeOnDelete();
            $table->foreignId('id_menu')->constrained('menu', 'id_menu')->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('note')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
