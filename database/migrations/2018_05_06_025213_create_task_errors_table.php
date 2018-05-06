<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->string('HTTPCode')->nullable();
            $table->integer('task_request_id')->unsigned();
            $table->foreign('task_request_id')->references('id')->on('task_requests')->onDelete('cascade');
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
        Schema::dropIfExists('task_errors');
    }
}
