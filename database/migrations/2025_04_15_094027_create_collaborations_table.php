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
        Schema::create('collaborations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['chat', 'whiteboard', 'video']);
            $table->bigInteger('group_id')->index('idx_collaborations_group_id');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborations');
    }
};
