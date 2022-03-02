<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('task')->nullable();
            $table->string('topic')->nullable();
            $table->string('instructions')->nullable();
            $table->dateTime('task_given_on')->nullable();
            $table->dateTime('task_started_on')->nullable();
            $table->dateTime('task_submitted_on')->nullable();
            $table->string('word_limit')->nullable();
            $table->string('time_limit')->nullable();
            $table->string('file_path')->nullable();
            $table->string('word_count')->nullable();
            $table->uuid('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('admin_id');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('region_target')->constrained('regions')->onDelete('cascade');
            $table->foreignId('website_id')->constrained('websites')->onDelete('cascade');
            $table->enum('task_type', ['Internal', 'External'])->default('Internal');
            $table->string('payout_amount')->nullable();
            $table->string('payout_id')->nullable();
            $table->string('published_date')->nullable();
            $table->string('published_url')->nullable();
            $table->foreignId('link_id')->nullable()->constrained('links')->onDelete('cascade');
            $table->enum('status', ['Submitted', 'Pending', 'Correction Required', 'Approved','Cancelled','Acknowledged','Feedback'])->default('Pending');
            $table->string('feedback')->nullable();
            $table->string('admin_notes')->nullable();
            $table->string('editor_notes')->nullable();
            $table->string('writer_notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('references')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
