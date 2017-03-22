<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentCvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_cvs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->string('name');
            $table->string('avatar');
            $table->string('position');
            $table->date('dateofbirth');
            $table->tinyInteger('gender');
            $table->integer('phone');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('link');
            $table->string('intent');
            $table->string('skill');
            $table->string('hobby');
            $table->date('year_start');
            $table->date('year_stop');
            $table->smallInteger('grade');
            $table->string('school');
            $table->string('major');
            $table->float('cpa');
            $table->string('majorskill');
            $table->string('otherskill');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('student_cvs');
    }
}
