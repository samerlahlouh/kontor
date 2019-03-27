<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('selected_packet_id')->nullable();
            $table->foreign('selected_packet_id')->references('id')->on('packets')
                    ->onDelete('cascade')->onUpdate('cascade');
                    
            $table->enum('status', ['check_pending', 'selecting_packet', 'in_review', 'in_progress', 'rejected', 'completed', 'canceled'])->nullable();
            $table->string('mobile')->nullable();
            $table->string('operator')->nullable();
            $table->double('operator_price', 8, 2)->nullable();
            $table->double('admin_price', 8, 2)->nullable();
            $table->double('user_price', 8, 2)->nullable();
            $table->string('customer_name');
            $table->enum('type', ['charge', 'transfer'])->nullable();
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
        Schema::dropIfExists('orders');
    }
}
