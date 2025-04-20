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
        Schema::create('echo_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content');
            $table->enum('source_type', ['chat', 'whiteboard', 'question']);
            $table->text('source_content');
            $table->bigInteger('lesson_id')->index('idx_echo_cards_lesson_id');
            $table->bigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echo_cards');
    }
};
