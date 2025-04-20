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
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign(['course_id'], 'groups_course_id_fkey')->references(['id'])->on('courses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['created_by'], 'groups_created_by_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign('groups_course_id_fkey');
            $table->dropForeign('groups_created_by_fkey');
        });
    }
};
