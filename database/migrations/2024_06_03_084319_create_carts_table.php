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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('salesman_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('productName')->nullable();
            $table->string('productPrice')->nullable();
            $table->string('productAddon')->nullable();
            $table->string('addonPrice')->nullable(); 
            $table->string('productVariation')->nullable();
            $table->string('VariationPrice')->nullable();
            $table->string('drinkFlavour')->nullable();
            $table->string('drinkFlavourPrice')->nullable();
            $table->string('productQuantity')->nullable();
            $table->string('totalPrice')->nullable();
            $table->integer('order_status')->nullable();
            
            $table->foreign('salesman_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('dine_in_tables')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
