<?php

use Botble\ACL\Models\User;
use Botble\Blog\Models\Category;
use Botble\Slug\Models\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class SeedBlogCategoriesForRedesign extends Migration
{
    /**
     * The blog listing redesign's category filter bar and topic tiles need
     * these real categories to filter by - only "News" existed before.
     * Idempotent: skips any name that already exists.
     *
     * @return void
     */
    public function up()
    {
        $names = ['Buying', 'Selling', 'Investment', 'Property Guide', 'Market Trends'];
        $authorId = User::query()->value('id') ?: 1;

        foreach ($names as $order => $name) {
            if (Category::query()->where('name', $name)->exists()) {
                continue;
            }

            $category = Category::query()->create([
                'name'        => $name,
                'status'      => 'published',
                'order'       => $order,
                'author_id'   => $authorId,
                'author_type' => User::class,
            ]);

            Slug::query()->create([
                'key'             => Str::slug($name),
                'reference_type'  => Category::class,
                'reference_id'    => $category->id,
                'prefix'          => '',
            ]);
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        $names = ['Buying', 'Selling', 'Investment', 'Property Guide', 'Market Trends'];

        $categories = Category::query()->whereIn('name', $names)->get();

        foreach ($categories as $category) {
            Slug::query()
                ->where('reference_type', Category::class)
                ->where('reference_id', $category->id)
                ->delete();

            $category->delete();
        }
    }
}
