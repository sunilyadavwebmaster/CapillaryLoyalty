<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapilleryAuthenticateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('capillery_authenticates')) {
            Schema::create('capillery_authenticates', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->length(11);
                $table->unsignedBigInteger('user_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');  
                $table->text('app_key')->length(20);     
                $table->text('client_secret_key')->length(20);
                $table->text('base_url')->length(30);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capillery_authenticates');
    }
}
