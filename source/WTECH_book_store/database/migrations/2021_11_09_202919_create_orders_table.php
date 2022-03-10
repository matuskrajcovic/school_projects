<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('address_id');

            $table->string('name', 100);
            $table->string('email', 100);

            $table->enum('shipping_type', ['post_to_address', 'post_to_post', 'courier', 'bookomat', 'to_branch']);
            $table->string('note', 256)->nullable();
            $table->unsignedInteger('count');
            $table->unsignedFloat('price');
            $table->boolean('payed')->default(false);
            $table->boolean('shipped')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
