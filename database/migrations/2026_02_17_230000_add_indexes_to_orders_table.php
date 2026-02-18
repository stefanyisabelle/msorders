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
            // Index for filtering by status (most common filter)
            $table->index('status');
            
            // Index for filtering by destination
            $table->index('destination');
            
            // Composite index for date range queries
            $table->index(['departure_date', 'return_date']);
            
            // Composite index for user's orders with status
            $table->index(['user_id', 'status']);
            
            // Index for sorting by created_at
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['destination']);
            $table->dropIndex(['departure_date', 'return_date']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
        });
    }
};
