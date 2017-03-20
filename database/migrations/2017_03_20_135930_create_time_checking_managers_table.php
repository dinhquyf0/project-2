<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeCheckingManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_checking_managers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->tinyInteger('status');
            $table->integer('student_id', false, true)->unsigned();
            $table->integer('employee_id', false, true)->unsigned();
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('time_checking_managers');
    }
}
