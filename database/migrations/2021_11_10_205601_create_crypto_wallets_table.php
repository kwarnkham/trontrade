<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->text('private_key');
            $table->text('public_key')->nullable();
            $table->string('base58_check')->index()->nullable();
            $table->string('hex_address')->index()->nullable();
            $table->text('base64')->nullable();
            $table->foreignId('crypto_network_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('activated_at')->nullable();
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
        Schema::dropIfExists('crypto_wallets');
    }
}
