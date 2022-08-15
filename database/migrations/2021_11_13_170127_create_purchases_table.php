<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('token_id')->constrained();
            $table->foreignId('payment_id')->nullable()->constrained();
            $table->string('from');
            $table->string('to');
            $table->double('amount');
            $table->double('unit_price');
            $table->string('transaction_id')->nullable()->index();
            $table->tinyInteger('status')->default(1)->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('dealt_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_username')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('mobile')->nullable();
            $table->string('sale_account')->nullable();
            $table->string('sale_bank_name')->nullable();
            $table->string('sale_bank_username')->nullable();
            $table->string('sale_bank_branch')->nullable();
            $table->string('sale_mobile')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
