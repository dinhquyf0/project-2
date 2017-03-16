<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('categories');
            $table->string('desciption');
            $table->string('no_interns');
            $table->string('content');
            $table->string('timelimit');
            $table->string('status');
            $table->date('start');
            $table->date('stop');
            $table->integer('employeesid')->unsigned();
            $table->foreign('employeesid')->references('id')->on('users');
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
        Schema::drop('topics');
    }
}
