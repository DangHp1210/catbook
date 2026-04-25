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
        Schema::create('book_authors', function (Blueprint $table) {
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('author_id');

            $table->primary(['book_id', 'author_id']);

            $table->foreign('book_id', 'fk_book_authors_book')
                ->references('id')
                ->on('books')
                ->cascadeOnDelete();

            $table->foreign('author_id', 'fk_book_authors_author')
                ->references('id')
                ->on('authors')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_authors');
    }
};
