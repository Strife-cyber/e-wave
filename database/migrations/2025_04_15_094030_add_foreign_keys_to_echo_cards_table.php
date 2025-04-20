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
        Schema::table('echo_cards', function (Blueprint $table) {
            $table->foreign(['created_by'], 'echo_cards_created_by_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['lesson_id'], 'echo_cards_lesson_id_fkey')->references(['id'])->on('lessons')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echo_cards', function (Blueprint $table) {
            $table->dropForeign('echo_cards_created_by_fkey');
            $table->dropForeign('echo_cards_lesson_id_fkey');
        });
    }
};
