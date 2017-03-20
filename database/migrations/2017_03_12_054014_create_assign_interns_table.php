<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignInternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_interns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teachersid')->unsigned();
            $table->string('period');
            $table->integer('studentsid')->unsigned();
            $table->integer('employeesid')->unsigned();
            $table->foreign('studentsid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teachersid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employeesid')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('assign_interns');
    }
}
