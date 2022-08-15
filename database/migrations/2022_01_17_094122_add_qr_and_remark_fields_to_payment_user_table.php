<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrAndRemarkFieldsToPaymentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_user', function (Blueprint $table) {
            $table->string('qr')->nullable();
            $table->text('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_user', function (Blueprint $table) {
            $table->dropColumn(['qr', 'remark']);
        });
    }
}
