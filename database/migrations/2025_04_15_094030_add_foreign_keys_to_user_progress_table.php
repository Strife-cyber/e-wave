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
        Schema::table('user_progress', function (Blueprint $table) {
            $table->foreign(['lesson_id'], 'user_progress_lesson_id_fkey')->references(['id'])->on('lessons')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'user_progress_user_id_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropForeign('user_progress_lesson_id_fkey');
            $table->dropForeign('user_progress_user_id_fkey');
        });
    }
};
