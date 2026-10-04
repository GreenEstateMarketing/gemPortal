<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogSubscribersTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('blog_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('token', 60)->unique();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_subscribers');
    }
}
