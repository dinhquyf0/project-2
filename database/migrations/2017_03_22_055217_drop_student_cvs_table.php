<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStudentCvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_cvs', function (Blueprint $table) {
            Schema::dropIfExists('student_cvs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_cvs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->string('cv_id');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

        });
    }
}
