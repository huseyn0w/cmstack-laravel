<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Fix #9: previously duplicated route names (last-wins) produced wrong route()
 * URLs. Each name must now resolve to a single, distinct URI.
 */
class RouteNamesTest extends TestCase
{
    public function test_post_route_names_are_distinct(): void
    {
        $posts = route('posts', ['slug' => 'hello']);
        $postsLocalized = route('posts_localized', ['locale' => 'ru', 'slug' => 'hello']);

        $this->assertStringEndsWith('/posts/hello', $posts);
        $this->assertStringContainsString('/ru/posts/hello', $postsLocalized);
        $this->assertNotSame($posts, $postsLocalized);
    }

    public function test_category_route_names_are_distinct(): void
    {
        $category = route('categories_first_page', ['slug' => 'news']);
        $categoryLocalized = route('categories_localized', ['locale' => 'ru', 'slug' => 'news']);

        $this->assertStringEndsWith('/category/news', $category);
        $this->assertStringContainsString('/ru/category/news', $categoryLocalized);
        $this->assertNotSame($category, $categoryLocalized);
    }

    public function test_no_duplicate_route_names_registered(): void
    {
        $names = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name !== null) {
                $names[] = $name;
            }
        }

        $duplicates = array_filter(array_count_values($names), fn ($count) => $count > 1);

        $this->assertSame([], $duplicates, 'Duplicate route names: '.implode(', ', array_keys($duplicates)));
    }
}
