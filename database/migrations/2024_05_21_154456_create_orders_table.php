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
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('assign_to_rider')->nullable();
            $table->unsignedBigInteger('salesman_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('total_bill')->nullable();
            $table->decimal('taxes', 8, 2)->nullable()->default(0.0);
            $table->string('delivery_charge')->nullable();
            $table->decimal('discount', 8, 2)->nullable()->default(0.0);
            $table->string('discount_reason')->nullable()->default('None');
            $table->string('discount_type')->nullable()->default('None');
            $table->string('payment_method')->nullable();
            $table->decimal('received_cash', 8, 2)->nullable();
            $table->decimal('return_change', 8, 2)->nullable();
            $table->string('ordertype')->nullable()->nullable();
            $table->string('order_address')->nullable();
            $table->integer('status')->default('2')->nullable();
            $table->integer('delivery_status')->default('0')->nullable();
            $table->string('order_cancel_by')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->foreign('assign_to_rider')->references('id')->on('riders');
            $table->foreign('table_id')->references('id')->on('dine_in_tables')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('salesman_id')->references('id')->on('users')->onDelete('cascade');
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
