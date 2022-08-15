<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoWalletTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_wallet_token', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_wallet_id')->constrained();
            $table->foreignId('token_id')->constrained();
            $table->double('balance')->default(0);
            $table->timestamps();
            $table->unique(['crypto_wallet_id', 'token_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_wallet_token');
    }
}
