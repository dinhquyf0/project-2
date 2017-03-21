<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('username')->length(50)->unique();
            $table->string('password')->length(60);
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->length(50)->unique();
            $table->integer('groupid')->unsigned();
            $table->integer('phonenumber');
            $table->date('dateofbirth');
            $table->tinyInteger('lock');
            $table->tinyInteger('status');
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('groupid')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
