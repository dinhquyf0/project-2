<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emplyees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employeeid')->unique();;
            $table->string('position');
            $table->string('dept');
            $table->integer('companiesid')->unsigned();
            $table->foreign('companiesid')->references('id')->on('companies');
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
        Schema::drop('emplyees');
    }
}
