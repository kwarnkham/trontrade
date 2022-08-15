<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworkTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->bigInteger('block_number')->index();
            $table->bigInteger('block_timestamp')->index();
            $table->string('type')->index();
            $table->string('from')->index();
            $table->string('to')->index();
            $table->foreignId('token_id')->constrained();
            $table->double('amount');
            $table->double('fees');
            $table->jsonb('receipt');
            $table->text("contract_result")->nullable();
            $table->string('status');
            $table->foreignId('crypto_network_id')->constrained();
            $table->foreignId('recordable_id')->nullable()->index();
            $table->string('recordable_type')->nullable()->index();
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
        Schema::dropIfExists('network_transactions');
    }
}
