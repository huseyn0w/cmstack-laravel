<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CPanelPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            ['id' => 1],
        ]);

        DB::table('post_translations')->insert([
            [
                'title' => 'Post example',
                'slug' => 'post-example',
                'author_id' => 1,
                'post_id' => 1,
                'locale' => 'en',
                'status' => 1,
                'preview' => 'Preview of the first post',
                'content' => 'Content of the first post',
                'meta_keywords' => 'post, first post, description of first post',
                'meta_description' => 'First post meta description',
                'thumbnail' => env('APP_URL').'/filemanager/images/5dbb5723de46f.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Пример поста',
                'slug' => 'primer-posta',
                'author_id' => 1,
                'post_id' => 1,
                'locale' => 'ru',
                'status' => 1,
                'preview' => 'Превью примерочного поста',
                'content' => 'Контент примерочного поста',
                'meta_keywords' => 'пост, примерочный пост',
                'meta_description' => 'описание примерочного поста',
                'thumbnail' => env('APP_URL').'/filemanager/images/5dbb5723de46f.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
