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
        // Get the GE course ID
        $geCourse = \App\Models\Course::where('course_code', 'GE')->first();
        
        if ($geCourse) {
            // Update users with GE course_id
            \DB::table('users')
                ->where('course_id', $geCourse->id)
                ->update(['is_universal' => true]);
                
            // Update unverified_users with GE course_id
            \DB::table('unverified_users')
                ->where('course_id', $geCourse->id)
                ->update(['is_universal' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
