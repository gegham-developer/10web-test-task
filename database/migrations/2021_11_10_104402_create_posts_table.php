<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('scraped_post_id');
            $table->string('title')->index();
            $table->string('author')->index();
            $table->string('image');
            $table->text('excerpt');
            $table->timestamp('scraped_date')->useCurrent();
            $table->timestamp('article_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
