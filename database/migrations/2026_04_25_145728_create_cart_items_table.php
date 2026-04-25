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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('book_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('cart_id', 'fk_cart_items_cart')
                ->references('id')
                ->on('cart')
                ->cascadeOnDelete();

            $table->foreign('book_id', 'fk_cart_items_book')
                ->references('id')
                ->on('books')
                ->cascadeOnDelete();

            $table->unique(['cart_id', 'book_id'], 'uk_cart_book');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
