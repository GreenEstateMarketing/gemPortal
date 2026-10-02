<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillBlogPostSlugPrefix extends Migration
{
    public function up()
    {
        DB::table('slugs')
            ->where('reference_type', \Botble\Blog\Models\Post::class)
            ->where(function ($query) {
                $query->whereNull('prefix')->orWhere('prefix', '');
            })
            ->update(['prefix' => 'blog']);
    }

    public function down()
    {
        DB::table('slugs')
            ->where('reference_type', \Botble\Blog\Models\Post::class)
            ->where('prefix', 'blog')
            ->update(['prefix' => '']);
    }
}
