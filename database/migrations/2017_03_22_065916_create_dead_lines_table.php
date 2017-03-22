<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeadLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dead_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->date('company_register_topic')->nullable();
            $table->date('student_register_topic')->nullable();
            $table->date('company_rate')->nullable();
            $table->date('mark')->nullable();
            $table->date('company_report')->nullable();
            $table->date('student_report')->nullable();
            $table->string('period');
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
        Schema::drop('dead_lines');
    }
}
