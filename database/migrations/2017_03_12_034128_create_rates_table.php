<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('studentsid')->unsigned();
            $table->integer('teachersid')->unsigned();
            $table->integer('point');
            $table->string('rate');
            $table->string('period');
            $table->foreign('studentsid')->references('id')->on('users');
            $table->foreign('teachersid')->references('id')->on('users');
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
        Schema::drop('rates');
    }
}
