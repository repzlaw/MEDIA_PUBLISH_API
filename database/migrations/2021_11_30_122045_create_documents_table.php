<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // $table->foreignId('task_message_id')->constrained('task_messagings')->onDelete('cascade');
            $table->string('task_message_id');
            $table->string('document_upload_path');
            $table->enum('status', ['Submitted', 'Copy Passed', 'Copy Failed', 'Feedback', 'Accepted', 'Published'])->default('Submitted');
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
        Schema::dropIfExists('documents');
    }
}
