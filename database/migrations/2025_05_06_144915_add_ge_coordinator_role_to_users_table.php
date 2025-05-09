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
        // No schema changes needed, just adding a comment
        // Role values: 0 = Instructor, 1 = Chairperson, 2 = Dean, 3 = Admin, 4 = GE Coordinator
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No schema changes were made, so nothing to rollback
    }
};
