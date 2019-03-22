<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chargings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
                    
            $table->enum('type', ['eft', 'cash', 'credit', 'pay_off'])->nullable();
            $table->enum('status', ['in_waiting', 'accepted', 'rejected'])->nullable();
            $table->double('amount', 8, 2)->nullable();
            $table->double('balance_before', 8, 2)->nullable()->default('0');
            $table->double('balance_after', 8, 2)->nullable()->default('0');
            $table->dateTime('request_date')->nullable();
            $table->dateTime('response_date')->nullable();
            $table->longText('notes')->nullable();
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
        Schema::dropIfExists('chargings');
    }
}
