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
        Schema::create('chat_ai_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id')->unique();
            $table->string('model_name');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->integer('response_time_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('message_id', 'fk_chat_ai_logs_message')
                ->references('id')
                ->on('chat_messages')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_ai_logs');
    }
};
