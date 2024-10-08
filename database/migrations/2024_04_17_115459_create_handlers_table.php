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
        Schema::create('handlers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('product_quantity')->nullable();
            $table->string('product_total_price')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handlers');
    }
};
