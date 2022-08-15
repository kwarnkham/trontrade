<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentifierUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identifier_user', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('number');
            $table->string('sub_number')->nullable();
            $table->jsonb('images');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('identifier_id')->constrained();
            $table->unique(['user_id', 'identifier_id']);
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
        Schema::dropIfExists('identifier_user');
    }
}
