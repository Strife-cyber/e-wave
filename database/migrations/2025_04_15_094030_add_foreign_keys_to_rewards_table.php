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
        Schema::table('rewards', function (Blueprint $table) {
            $table->foreign(['badge_id'], 'rewards_badge_id_fkey')->references(['id'])->on('badges')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['user_id'], 'rewards_user_id_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign('rewards_badge_id_fkey');
            $table->dropForeign('rewards_user_id_fkey');
        });
    }
};
