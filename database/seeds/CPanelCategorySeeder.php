<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CPanelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['id' => 1], ['id' => 2],
        ]);

        DB::table('category_translations')->insert([
            [
                'category_id' => 1,
                'locale' => 'en',
                'title' => 'Main parent category',
                'slug' => 'parent_category',
                'description' => 'This is main category',
                'meta_description' => 'Main parent category',
                'meta_keywords' => 'main category, category, parent category',
            ],
            [
                'category_id' => 1,
                'locale' => 'ru',
                'title' => 'Родительская категория',
                'slug' => 'roditelskaya_kateqoriya',
                'description' => 'Описание родительской категории',
                'meta_description' => 'Главная родительская категория',
                'meta_keywords' => 'главная категория, категория, родительская категория',
            ],
            [
                'category_id' => 2,
                'locale' => 'en',
                'title' => 'About',
                'slug' => 'about',
                'description' => 'Here is news from About category',
                'meta_description' => 'About category',
                'meta_keywords' => 'about, category',
            ],
            [
                'category_id' => 2,
                'locale' => 'ru',
                'title' => 'О проекте',
                'slug' => 'o_proyekte',
                'description' => 'Тут распологаются новости проекта',
                'meta_description' => 'О проекте',
                'meta_keywords' => 'о проекте, категория',
            ],
        ]);

    }
}
