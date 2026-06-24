<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('author_id');
            $table->string('locale')->index();

            $table->string('title', 50);
            $table->string('slug', 50);
            $table->string('thumbnail')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('status');
            $table->mediumText('preview')->nullable();
            $table->mediumText('content')->nullable();
            $table->integer('likes')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['post_id', 'locale']);
            $table->unique(['locale', 'title', 'slug']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_translations');
    }
}
