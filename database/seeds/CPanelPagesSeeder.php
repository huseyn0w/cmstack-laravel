<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CPanelPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $home_page_custom_fields = [
            'en' => [
                'headline' => [
                    'value' => 'Cmstack-Laravel',
                    'type' => 'text',
                    'admin_label' => 'Headline',
                ],
                'headline-image' => [
                    'value' => env('APP_URL').'/filemanager/images/5d9ca59b897a2.jpg',
                    'type' => 'image',
                    'admin_label' => 'Headline Image',
                ],
                'posts-from-category-headline' => [
                    'value' => 'Hot topics from Travel Section',
                    'type' => 'text',
                    'admin_label' => 'Posts from Category Headline',
                ],
                'posts-from-category-description' => [
                    'value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'type' => 'text',
                    'admin_label' => 'Posts from Category description',
                ],
                'posts-from-category-cat-id' => [
                    'value' => '1',
                    'type' => 'category',
                    'admin_label' => 'Choose category',
                ],
                'about-headline' => [
                    'value' => 'About Us',
                    'type' => 'text',
                    'admin_label' => 'About Headline',
                ],
                'about-description' => [
                    'value' => 'Who exactly create this CMS?',
                    'type' => 'text',
                    'admin_label' => 'About description',
                ],
                'about-big-text' => [
                    'value' => '<p><strong>Elman Hüseynov</strong> - Full Stack Web Developer with more than 3 years of experience at freelance/office/remote jobs, completed more than 50 of projects and websites from scratch, currently Remote Full Stack Web Developer - located in Baku / Azerbaijan.</p>',
                    'type' => 'textarea',
                    'admin_label' => 'About Full Description',
                ],
                'authors' => [
                    'type' => 'repeater',
                    'admin_label' => 'Authors',
                    'value' => [
                        'row-0' => [
                            'author-image' => [
                                'value' => env('APP_URL').'/filemanager/images/5dbb536d16ce8.JPG',
                                'type' => 'image',
                                'admin_label' => 'Author Image',
                            ],
                            'author-name' => [
                                'value' => 'Elman Hüseynov',
                                'type' => 'text',
                                'admin_label' => 'Author Name',
                            ],
                            'author-position' => [
                                'value' => 'Cmstack-Laravel Author',
                                'type' => 'text',
                                'admin_label' => 'Author Position',
                            ],
                            'author-linkedin' => [
                                'value' => [
                                    'label' => '#',
                                    'url' => 'https://linkedin.com/in/huseyn0w/',
                                    'target' => '1',
                                ],
                                'type' => 'link',
                                'admin_label' => 'Author Linkedin',
                            ],
                        ],
                    ],
                ],

            ],
            'ru' => [
                'headline' => [
                    'value' => 'Cmstack-Laravel',
                    'type' => 'text',
                    'admin_label' => 'Заголовок',
                ],
                'headline-image' => [
                    'value' => env('APP_URL').'/filemanager/images/5d9ca59b897a2.jpg',
                    'type' => 'image',
                    'admin_label' => 'Заголовок изображения',
                ],
                'posts-from-category-headline' => [
                    'value' => 'Свежие новости с главной категории',
                    'type' => 'text',
                    'admin_label' => 'Заголовок секции постов с категории',
                ],
                'posts-from-category-description' => [
                    'value' => 'Описание будет тут',
                    'type' => 'text',
                    'admin_label' => 'Описание секции постов с категории',
                ],
                'posts-from-category-cat-id' => [
                    'value' => '1',
                    'type' => 'category',
                    'admin_label' => 'Выберите категорию',
                ],
                'about-headline' => [
                    'value' => 'О нас',
                    'type' => 'text',
                    'admin_label' => 'Заголовок раздела о нас',
                ],
                'about-description' => [
                    'value' => 'Немного об авторах',
                    'type' => 'text',
                    'admin_label' => 'Краткое описание раздела об авторах',
                ],
                'about-big-text' => [
                    'value' => '<p><strong>Эльман Гусейнов</strong> - Full Stack Web Разработчик с опытом работы более 3 лет в различных сферах начиная от фрилансера, заканчивая удаленной разработкой проектов, создал более 50 проектов с нуля, в данный момент является удаленным разработчиком - находится в Баку / Азербайджан.</p>',
                    'type' => 'textarea',
                    'admin_label' => 'Подробное описание раздела об авторах',
                ],
                'authors' => [
                    'type' => 'repeater',
                    'admin_label' => 'Authors',
                    'value' => [
                        'row-0' => [
                            'author-image' => [
                                'value' => env('APP_URL').'/filemanager/images/5dbb536d16ce8.JPG',
                                'type' => 'image',
                                'admin_label' => 'Изображение автора',
                            ],
                            'author-name' => [
                                'value' => 'Elman Hüseynov',
                                'type' => 'text',
                                'admin_label' => 'Имя автора',
                            ],
                            'author-position' => [
                                'value' => 'Создатель Cmstack-Laravel',
                                'type' => 'text',
                                'admin_label' => 'Должность',
                            ],
                            'author-linkedin' => [
                                'value' => [
                                    'label' => '#',
                                    'url' => 'https://linkedin.com/in/huseyn0w/',
                                    'target' => '1',
                                ],
                                'type' => 'link',
                                'admin_label' => 'Linkedin',
                            ],
                        ],
                    ],
                ],

            ],
        ];

        DB::table('pages')->insert([
            ['id' => 1],
            ['id' => 2],
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
                'meta_description' => 'This is homepage of CMS Cmstack-Laravel',
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
                'meta_description' => 'Это главная страница CMS Cmstack-Laravel',
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
