<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('page_id');
            $table->string('locale')->index();

            $table->string('title', 50);
            $table->string('slug', 50);
            $table->unsignedBigInteger('author_id');
            $table->foreign('author_id')->references('id')->on('users');
            $table->integer('status');
            $table->mediumText('content')->nullable();
            $table->mediumText('custom_fields')->json()->nullable();
            $table->string('template')->default('page');
            $table->string('meta_description');
            $table->string('meta_keywords');
            $table->timestamps();

            $table->unique(['page_id', 'locale']);
            $table->unique(['locale', 'title', 'slug']);
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_translations');
    }
}
