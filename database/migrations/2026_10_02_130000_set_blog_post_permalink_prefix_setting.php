<?php

use Botble\Blog\Models\Post;
use Botble\Slug\SlugHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetBlogPostPermalinkPrefixSetting extends Migration
{
    public function up()
    {
        $key = app(SlugHelper::class)->getPermalinkSettingKey(Post::class);

        DB::table('settings')->updateOrInsert(['key' => $key], ['value' => 'blog']);

        // Catch any post slugs saved with an empty prefix before this setting
        // existed (admin's slug box falls back to an empty default without it).
        DB::table('slugs')
            ->where('reference_type', Post::class)
            ->where(function ($query) {
                $query->whereNull('prefix')->orWhere('prefix', '');
            })
            ->update(['prefix' => 'blog']);
    }

    public function down()
    {
        $key = app(SlugHelper::class)->getPermalinkSettingKey(Post::class);

        DB::table('settings')->where('key', $key)->delete();
    }
}
