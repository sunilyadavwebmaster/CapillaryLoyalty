<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressTrackersDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('progress_trackers_data')) {
            Schema::create('progress_trackers_data', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->length(11);
                $table->unsignedBigInteger('user_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
                $table->integer('min_value_progressbar')->length(11);
                $table->integer('max_value_progressbar')->length(5);
                $table->integer('total_tiers')->length(11);
                $table->text('tiers_data')->length(50);
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
        Schema::dropIfExists('progress_trackers_data');
    }
}
