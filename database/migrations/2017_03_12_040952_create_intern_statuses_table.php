<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intern_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('studentsid')->unsigned();
            $table->string('period');
            $table->smallInteger('status');
            $table->string('link_report');
            $table->foreign('studentsid')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('intern_statuses');
    }
}
