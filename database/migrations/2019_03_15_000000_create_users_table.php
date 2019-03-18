<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('created_by_user_id')->nullable()->default('1');
            $table->foreign('created_by_user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');

            $table->string('user_name')->unique();
            $table->string('password')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->nullable();
            $table->enum('type', ['admin', 'regular', 'agent'])->nullable();
            $table->double('balance', 8, 2)->nullable()->default('0');
            $table->double('credit', 8, 2)->nullable()->default('0');
            $table->boolean('is_active')->nullable()->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(
            array(
                'user_name' => 'samer.lahlouh',
                'password'  => Hash::make('samer5747'),
                'name'      => 'Samer Lahlouh',
                'email'     => 'samer@hotmail.com',
                'mobile'    => '05315984771',
                'type'      => 'admin'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
