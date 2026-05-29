<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // MySQL: Modify enum to include 'completed' and keep existing values
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'success', 'completed', 'failed', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'success', 'failed') DEFAULT 'pending'");
        }
    }
};
