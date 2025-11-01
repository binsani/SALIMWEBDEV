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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 100)->unique()->nullable();
            $table->string('short_description', 200)->nullable();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('product_type', ['physical', 'digital']);
            $table->integer('stock_quantity')->default(0);
            $table->string('digital_file_path', 500)->nullable();
            $table->bigInteger('digital_file_size')->nullable();
            $table->integer('download_limit')->nullable();
            $table->integer('download_expiry_hours')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('product_type');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
