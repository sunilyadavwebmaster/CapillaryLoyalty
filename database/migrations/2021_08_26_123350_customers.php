<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table){
            $table->boolean('cancel_transaction')->default(0);
            $table->boolean('enable_points')->default(0);     
            $table->integer('min_redeem_point')->nullable();
            $table->integer('max_redeem_point')->nullable();  
            $table->integer('multi_redeem_point_claim')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
