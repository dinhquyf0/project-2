<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employeesid')->unsigned();
            $table->integer('studentsid')->unsigned();
            $table->integer('point');
            $table->string('rate');
            $table->string('period');
            $table->foreign('studentsid')->references('id')->on('users');
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
        Schema::drop('company_rates');
    }
}
