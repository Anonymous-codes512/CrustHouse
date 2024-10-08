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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); 
            $table->string('productImage');
            $table->string('productName');
            $table->string('productVariation')->nullable();
            $table->string('productPrice');
            $table->string('category_name')->nullable(); 
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
