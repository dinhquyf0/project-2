<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStudentInternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_interns', function (Blueprint $table) {
            Schema::drop('student_interns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_interns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('topic_1')->unsigned()->nullable();
            $table->integer('topic_2')->unsigned()->nullable();
            $table->integer('topic_3')->unsigned()->nullable();
            $table->integer('topic_expect')->unsigned()->nullable();
            $table->foreign('topic_1')->references('id')->on('topics')->onDelete('cascade');
            $table->foreign('topic_2')->references('id')->on('topics')->onDelete('cascade');
            $table->foreign('topic_3')->references('id')->on('topics')->onDelete('cascade');
            $table->foreign('topic_expect')->references('id')->on('topics')->onDelete('cascade');
            $table->timestamps();
        });
    }
}
