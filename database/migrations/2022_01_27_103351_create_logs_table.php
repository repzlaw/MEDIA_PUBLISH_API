<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('message');
            $table->string('model');
            $table->string('model_id');
            $table->string('url');
            $table->string('status')->default('unseen');
            $table->uuid('reciever_id');
            $table->foreign('reciever_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('causer_id');
            $table->foreign('causer_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('logs');
    }
}
