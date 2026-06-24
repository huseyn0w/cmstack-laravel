<?php

namespace App\Repositories;

use App\Http\Models\Post;
use App\Http\Models\Tag;
use App\Http\Models\TagTranslation;
use Illuminate\Support\Str;

/**
 * Persistence for the translatable Tag taxonomy: find-or-create tags by name
 * (deriving a slug) and sync them to a post. All ORM access for tags lives here
 * so services/observers only delegate.
 */
class TagRepository extends BaseRepository
{
    protected $main_table = 'tags';

    protected $translated_table = 'tag_translations';

    protected $translated_table_join_column = 'tag_id';

    protected $select_fields = [
        'id',
        'name',
        'slug',
    ];

    public function __construct(Tag $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->translated_model = new TagTranslation;
    }

    /**
     * Find-or-create each named tag (current locale) and sync the post's tags
     * to exactly that set. Blank names are ignored. Passing an empty list
     * detaches all tags.
     *
     * @param  array<int, string>  $names
     */
    public function syncToPost(Post $post, array $names): void
    {
        $ids = [];

        foreach ($names as $name) {
            $name = trim((string) $name);

            if ($name === '') {
                continue;
            }

            $ids[] = $this->findOrCreateByName($name)->id;
        }

        $post->tags()->sync(array_values(array_unique($ids)));
    }

    /**
     * Resolve a tag by its slug (or name) in the active locale, creating it
     * when absent.
     */
    public function findOrCreateByName(string $name): Tag
    {
        $locale = app()->getLocale();
        $slug = Str::slug($name);

        $existing = $this->model->whereTranslation('slug', $slug, $locale)->first()
            ?? $this->model->whereTranslation('name', $name, $locale)->first();

        if ($existing) {
            return $existing;
        }

        return $this->model->create(['name' => $name, 'slug' => $slug]);
    }
}
