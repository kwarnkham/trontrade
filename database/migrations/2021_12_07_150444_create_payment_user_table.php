<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('payment_id')->constrained();
            $table->string('account');
            $table->string('bank_name')->nullable();
            $table->string('bank_username')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unique(['user_id', 'payment_id']);
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
        Schema::dropIfExists('payment_user');
    }
}
