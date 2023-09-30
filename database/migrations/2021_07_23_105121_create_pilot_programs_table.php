<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePilotProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pilot_programs')) {
            Schema::create('pilot_programs', function (Blueprint $table) {
               $table->engine = 'InnoDB';
                $table->increments('id')->length(11);
                $table->unsignedBigInteger('user_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
                $table->boolean('enabled')->default(0);
                $table->text('field_capillary')->length(20);
                $table->boolean('field_value_capillary')->default(0);
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
        Schema::dropIfExists('pilot_programs');
    }
}
