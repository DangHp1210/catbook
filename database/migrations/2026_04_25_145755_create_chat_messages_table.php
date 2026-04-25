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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->enum('sender_type', ['user', 'bot', 'admin']);
            $table->text('message_text');
            $table->enum('message_type', ['text', 'image', 'suggestion'])->default('text');
            $table->unsignedBigInteger('related_book_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('session_id', 'fk_chat_messages_session')
                ->references('id')
                ->on('chat_sessions')
                ->cascadeOnDelete();

            $table->foreign('related_book_id', 'fk_chat_messages_book')
                ->references('id')
                ->on('books')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
