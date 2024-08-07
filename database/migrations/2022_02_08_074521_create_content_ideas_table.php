<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentIdeasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_ideas', function (Blueprint $table) {
            $table->id();
            $table->longText('topic');
            $table->string('reference_url');
            $table->longText('description')->nullable();
            $table->foreignId('website_id')->nullable()->constrained('websites')->onDelete('cascade');
            $table->foreignId('external_website_id')->nullable()->constrained('external_websites')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('status')->default('Private');
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
        Schema::dropIfExists('content_ideas');
    }
}
