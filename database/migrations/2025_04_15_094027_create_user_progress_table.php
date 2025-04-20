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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index('idx_user_progress_user_id');
            $table->bigInteger('lesson_id')->index('idx_user_progress_lesson_id');
            $table->enum('status', ['started', 'completed', 'failed']);
            $table->integer('score');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
