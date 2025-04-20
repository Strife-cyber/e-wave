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
        Schema::table('collaboration_events', function (Blueprint $table) {
            $table->foreign(['collaboration_id'], 'collaboration_events_collaboration_id_fkey')->references(['id'])->on('collaborations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'collaboration_events_user_id_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaboration_events', function (Blueprint $table) {
            $table->dropForeign('collaboration_events_collaboration_id_fkey');
            $table->dropForeign('collaboration_events_user_id_fkey');
        });
    }
};
