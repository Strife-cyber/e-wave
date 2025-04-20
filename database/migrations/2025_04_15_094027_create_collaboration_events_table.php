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
        Schema::create('collaboration_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('collaboration_id')->index('idx_collaboration_events_collaboration_id');
            $table->bigInteger('user_id');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaboration_events');
    }
};
