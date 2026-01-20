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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->integer('qty');
            $table->string('status');
            $table->string('supplier_ref')->nullable();
            $table->unsignedTinyInteger('supplier_checks')->default(0);
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->integer('available_qty');
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->integer('qty');
            $table->string('type');
            $table->foreignId('order_id')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('inventories')->insert([
            'sku' => 'ABC123',
            'available_qty' => 10
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('orders');
    }
};
