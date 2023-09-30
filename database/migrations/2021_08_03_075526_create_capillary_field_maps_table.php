<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapillaryFieldMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('capillary_field_maps')) {
            Schema::create('capillary_field_maps', function (Blueprint $table) {
                $table->engine = 'InnoDb';
                $table->increments('id')->length(11);
                $table->unsignedBigInteger('user_id');
                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
                $table->string('mapping_role')->length(20);
                $table->boolean('status')->default(1);
                $table->string('field_type')->length(10);
                $table->string('shopify_field')->length(50);
                $table->string('capillary_field')->length(50);

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
        Schema::dropIfExists('capillary_field_maps');
    }
}
