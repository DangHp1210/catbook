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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('page_count')->nullable();
            $table->string('language', 100)->nullable();
            $table->integer('publication_year')->nullable();
            $table->enum('status', ['available', 'hidden', 'out_of_stock'])->default('available');
            $table->unsignedBigInteger('publisher_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('publisher_id', 'fk_books_publisher')
                ->references('id')
                ->on('publishers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
