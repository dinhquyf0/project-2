<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('studentid')->uniqued();
            $table->integer('schoolyear');
            $table->integer('classesid')->unsigned();
            $table->integer('grade', false);
            $table->integer('fromyear', false);
            $table->integer('toyear', false);
            $table->string('mayjor');
            $table->foreign('classesid')->references('id')->on('classes')->onDelete('cascade');
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
        Schema::drop('students');
    }
}
