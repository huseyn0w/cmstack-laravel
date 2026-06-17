<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CPanelPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $home_page_custom_fields = array (
            'en' => array (
                'headline' =>
                    array (
                        'value' => 'LaraPress CMS',
                        'type' => 'text',
                        'admin_label' => 'Headline',
                    ),
                'headline-image' =>
                    array (
                        'value' => env('APP_URL').'/filemanager/images/5d9ca59b897a2.jpg',
                        'type' => 'image',
                        'admin_label' => 'Headline Image',
                    ),
                'posts-from-category-headline' =>
                    array (
                        'value' => 'Hot topics from Travel Section',
                        'type' => 'text',
                        'admin_label' => 'Posts from Category Headline',
                    ),
                'posts-from-category-description' =>
                    array (
                        'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                        'type' => 'text',
                        'admin_label' => 'Posts from Category description',
                    ),
                'posts-from-category-cat-id' =>
                    array (
                        'value' => '1',
                        'type' => 'category',
                        'admin_label' => 'Choose category',
                    ),
                'about-headline' =>
                    array (
                        'value' => 'About Us',
                        'type' => 'text',
                        'admin_label' => 'About Headline',
                    ),
                'about-description' =>
                    array (
                        'value' => 'Who exactly create this CMS?',
                        'type' => 'text',
                        'admin_label' => 'About description',
                    ),
                'about-big-text' =>
                    array (
                        'value' => '<p><strong>Elman Hüseynov</strong> - Full Stack Web Developer with more than 3 years of experience at freelance/office/remote jobs, completed more than 50 of projects and websites from scratch, currently Remote Full Stack Wordpress Developer - located in Baku / Azerbaijan.</p>',
                        'type' => 'textarea',
                        'admin_label' => 'About Full Description',
                    ),
                'authors' =>
                    array (
                        'type' => 'repeater',
                        'admin_label' => 'Authors',
                        'value' =>
                            array (
                                'row-0' =>
                                    array (
                                        'author-image' =>
                                            array (
                                                'value' => env('APP_URL').'/filemanager/images/5dbb536d16ce8.JPG',
                                                'type' => 'image',
                                                'admin_label' => 'Author Image',
                                            ),
                                        'author-name' =>
                                            array (
                                                'value' => 'Elman Hüseynov',
                                                'type' => 'text',
                                                'admin_label' => 'Author Name',
                                            ),
                                        'author-position' =>
                                            array (
                                                'value' => 'LaraPress CMS Author',
                                                'type' => 'text',
                                                'admin_label' => 'Author Position',
                                            ),
                                        'author-linkedin' =>
                                            array (
                                                'value' =>
                                                    array (
                                                        'label' => '#',
                                                        'url' => 'https://linkedin.com/in/huseyn0w/',
                                                        'target' => '1',
                                                    ),
                                                'type' => 'link',
                                                'admin_label' => 'Author Linkedin',
                                            ),
                                    ),
                            ),
                    ),

            ),
            'ru' => array (
                'headline' =>
                    array (
                        'value' => 'LaraPress CMS',
                        'type' => 'text',
                        'admin_label' => 'Заголовок',
                    ),
                'headline-image' =>
                    array (
                        'value' => env('APP_URL').'/filemanager/images/5d9ca59b897a2.jpg',
                        'type' => 'image',
                        'admin_label' => 'Заголовок изображения',
                    ),
                'posts-from-category-headline' =>
                    array (
                        'value' => 'Свежие новости с главной категории',
                        'type' => 'text',
                        'admin_label' => 'Заголовок секции постов с категории',
                    ),
                'posts-from-category-description' =>
                    array (
                        'value' => 'Описание будет тут',
                        'type' => 'text',
                        'admin_label' => 'Описание секции постов с категории',
                    ),
                'posts-from-category-cat-id' =>
                    array (
                        'value' => '1',
                        'type' => 'category',
                        'admin_label' => 'Выберите категорию',
                    ),
                'about-headline' =>
                    array (
                        'value' => 'О нас',
                        'type' => 'text',
                        'admin_label' => 'Заголовок раздела о нас',
                    ),
                'about-description' =>
                    array (
                        'value' => 'Немного об авторах',
                        'type' => 'text',
                        'admin_label' => 'Краткое описание раздела об авторах',
                    ),
                'about-big-text' =>
                    array (
                        'value' => '<p><strong>Эльман Гусейнов</strong> - Full Stack Web Разработчик с опытом работы более 3 лет в различных сферах начиная от фрилансера, заканчивая удаленной разработкой проектов, создал более 50 проектов с нуля, в данный момент является удаленным разработчиком - находится в Баку / Азербайджан.</p>',
                        'type' => 'textarea',
                        'admin_label' => 'Подробное описание раздела об авторах',
                    ),
                'authors' =>
                    array (
                        'type' => 'repeater',
                        'admin_label' => 'Authors',
                        'value' =>
                            array (
                                'row-0' =>
                                    array (
                                        'author-image' =>
                                            array (
                                                'value' => env('APP_URL').'/filemanager/images/5dbb536d16ce8.JPG',
                                                'type' => 'image',
                                                'admin_label' => 'Изображение автора',
                                            ),
                                        'author-name' =>
                                            array (
                                                'value' => 'Elman Hüseynov',
                                                'type' => 'text',
                                                'admin_label' => 'Имя автора',
                                            ),
                                        'author-position' =>
                                            array (
                                                'value' => 'Создатель LaraPress CMS',
                                                'type' => 'text',
                                                'admin_label' => 'Должность',
                                            ),
                                        'author-linkedin' =>
                                            array (
                                                'value' =>
                                                    array (
                                                        'label' => '#',
                                                        'url' => 'https://linkedin.com/in/huseyn0w/',
                                                        'target' => '1',
                                                    ),
                                                'type' => 'link',
                                                'admin_label' => 'Linkedin',
                                            ),
                                    ),
                            ),
                    ),

            )
        );

        DB::table('pages')->insert([
            ['id' => 1],
            ['id' => 2]
        ]);

        DB::table('page_translations')->insert([
            [
                'title' => 'Homepage',
                'locale' => 'en',
                'page_id' => 1,
                'slug' => '/',
                'author_id' => 1,
                'status' => 1,
                'meta_keywords' => 'page, homepage',
                'meta_description' => 'This is homepage of CMS LaraPress',
                'custom_fields' => json_encode($home_page_custom_fields['en']),
                'template' => 'home',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Главная страница',
                'locale' => 'ru',
                'page_id' => 1,
                'slug' => '/',
                'author_id' => 1,
                'status' => 1,
                'meta_keywords' => 'страница, главная',
                'meta_description' => 'Это главная страница CMS LaraPress',
                'custom_fields' => json_encode($home_page_custom_fields['ru']),
                'template' => 'home',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'locale' => 'en',
                'page_id' => 2,
                'author_id' => 1,
                'status' => 1,
                'meta_keywords' => 'page, contact',
                'meta_description' => 'Contact page',
                'template' => 'contacts',
                'custom_fields' => json_encode([]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Контакты',
                'slug' => 'kontakti',
                'locale' => 'ru',
                'page_id' => 2,
                'author_id' => 1,
                'status' => 1,
                'meta_keywords' => 'страница, контакты',
                'meta_description' => 'Контактная страница',
                'template' => 'contacts',
                'custom_fields' => json_encode([]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
