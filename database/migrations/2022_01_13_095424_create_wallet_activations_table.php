<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletActivationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_wallet_id')->constrained();
            $table->foreignId('token_id')->constrained();
            $table->jsonb('activation_result');
            $table->string('from');
            $table->string('to');
            $table->double('amount');
            $table->string('transaction_id');
            $table->tinyInteger('status')->index()->default(1);
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
        Schema::dropIfExists('wallet_activations');
    }
}
