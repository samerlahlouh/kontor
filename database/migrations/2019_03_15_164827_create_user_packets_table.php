<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPacketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_packets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('packet_id')->nullable();
            $table->foreign('packet_id')->references('id')->on('packets')
                    ->onDelete('cascade')->onUpdate('cascade');
                    
            $table->double('admin_price', 8, 2)->nullable();
            $table->double('user_price', 8, 2)->nullable();
            $table->boolean('is_available')->nullable()->default(true);
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
        Schema::dropIfExists('user_packets');
    }
}
