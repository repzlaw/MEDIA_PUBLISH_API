<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Assigned', 'Copy Passed', 'Copy Failed', 'Acknowledged', 'Accepted', 'Changed','Editing', 'Document Submitted', 'Correction','Feedback','Published'])->default('Assigned');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->constrained('messagings')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('fields_From');
            $table->string('Fields_to');
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
        Schema::dropIfExists('event_logs');
    }
}
