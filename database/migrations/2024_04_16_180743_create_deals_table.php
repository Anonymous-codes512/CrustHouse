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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            
            $table->string('dealImage')->nullable();
            $table->string('dealTitle');
            $table->string('dealStatus');
            $table->string('dealActualPrice')->nullable();
            $table->string('dealDiscountedPrice')->nullable();
            $table->boolean('IsForever')->nullable();
            $table->string('dealEndDate')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
