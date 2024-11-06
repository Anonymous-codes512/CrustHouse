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
        Schema::table('orders', function (Blueprint $table) {
            // Add the table_id column and the foreign key constraint
            $table->unsignedBigInteger('table_id')->nullable()->after('branch_id'); // Replace 'some_column' with the column after which you want to add table_id
            $table->foreign('table_id')->references('id')->on('dine_in_tables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback: drop the foreign key and column
            $table->dropForeign(['table_id']);
            $table->dropColumn('table_id');
        });
    }
};
