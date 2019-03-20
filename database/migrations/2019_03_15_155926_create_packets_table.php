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
            $table->string('name')->nullable();
            $table->enum('operator', ['turkcell', 'vodafone'])->nullable();
            $table->unsignedInteger('sms')->nullable();
            $table->unsignedInteger('minutes')->nullable();
            $table->unsignedInteger('internet')->nullable();
            $table->enum('type', ['internet', 'minutes', 'tl', 'combo', 'packet'])->nullable();
            $table->double('price', 8, 2)->nullable();
            $table->boolean('is_global')->nullable()->default(true);
            $table->boolean('is_teens')->nullable()->default(false);
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
