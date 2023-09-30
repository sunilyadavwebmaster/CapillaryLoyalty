<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
             $table->engine = 'InnoDB';
                $table->increments('id')->length(11);
                $table->unsignedBigInteger('user_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
                $table->boolean('mobile_otp_enable')->default(0);
                $table->text('mobile_otp_attr')->length(20);
                $table->boolean('email_otp_enable')->default(0);
                $table->text('email_otp_attr')->length(30);
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
        Schema::dropIfExists('otp_verifications');
    }
}
