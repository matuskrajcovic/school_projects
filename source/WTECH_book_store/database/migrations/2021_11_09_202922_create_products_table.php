<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->startingValue(1000000);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->enum('product_type', ['book', 'e_book', 'audio_book', 'merchandice']);
            $table->string('name', 100);
            $table->unsignedFloat('price');
            $table->boolean('available')->default(true);
            $table->string('main_photo', 100)->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
