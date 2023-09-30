<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartRedeemPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_redeem_points', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->length(11);
            $table->string('points')->length(50);
            $table->string('token')->length(300);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_redeem_points');
    }
}
