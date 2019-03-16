<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packets', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('operator', ['turkcell', 'vodafone'])->nullable();
            $table->string('sms')->unique();
            $table->string('minutes')->unique();
            $table->string('internet')->unique();
            $table->enum('type', ['internet', 'minutes', 'tl', 'combo', 'packet'])->nullable();
            $table->double('price', 8, 2)->nullable();
            $table->boolean('is_global')->nullable();
            $table->boolean('is_teens')->nullable();
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
        Schema::dropIfExists('packets');
    }
}
