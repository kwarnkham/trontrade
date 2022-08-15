<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->string('api_url');
            $table->text('api_key')->nullable();
            $table->double('trade_fees')->default(1);
            $table->double('withdraw_fees')->default(1);
            $table->string('address')->nullable();
            $table->double('balance')->default(0);
            $table->jsonb('resources')->nullable();
            $table->text('private_key')->nullable();
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
        Schema::dropIfExists('crypto_networks');
    }
}
