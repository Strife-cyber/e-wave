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
        Schema::table('group_messages', function (Blueprint $table) {
            $table->foreign(['group_id'], 'group_messages_group_id_fkey')->references(['id'])->on('groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['user_id'], 'group_messages_user_id_fkey')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropForeign('group_messages_group_id_fkey');
            $table->dropForeign('group_messages_user_id_fkey');
        });
    }
};
