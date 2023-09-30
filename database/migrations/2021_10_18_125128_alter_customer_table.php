<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('creation')->nullable()->change();
            $table->boolean('updation')->nullable()->change();
            $table->boolean('fetch')->nullable()->change();
            $table->boolean('grouping')->nullable()->change();
            $table->boolean('add_transaction')->nullable()->change();
            $table->boolean('enable_points')->nullable()->change();
            $table->integer('min_redeem_point')->nullable()->change();
            $table->integer('max_redeem_point')->nullable()->change();
            $table->integer('multi_redeem_point_claim')->nullable()->change();
            $table->boolean('cancel_transaction')->nullable()->change();
            $table->boolean('transaction_mode')->nullable()->change();
            $table->boolean('mobile_otp')->nullable()->change();
            $table->string('mobile_otp_attribute')->nullable()->change();
            $table->boolean('email_otp')->nullable()->change();
            $table->string('email_otp_attribute')->nullable()->change();
            $table->string('min_val_progerss_bar')->nullable()->change();
            $table->string('max_val_progerss_bar')->nullable()->change();
            $table->string('total_num_tier')->nullable()->change();
            $table->string('tier_data')->nullable()->change();
            $table->boolean('pilot_program')->nullable()->change();
            $table->string('pilot_custom_field')->nullable()->change();
            $table->string('pilot_custom_field_value')->nullable()->change();
            $table->boolean('enable_coupon')->nullable()->change();
            $table->boolean('group_coupon')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
