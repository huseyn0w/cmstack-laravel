<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    /**
     * Domain VIEW service (a App\Services\BaseCrudService subclass) that owns all
     * data access for the front-end cluster. Controllers never call a repository
     * directly — the chain is Controller -> Service -> Repository. Left untyped
     * (like CPanelBaseController) so each subclass can assign its own concrete
     * domain service and call domain-specific methods on it.
     */
    protected $service;

    protected $data;

    public function __construct()
    {
        $this->lang_prefixes = get_lang_prefixes();
    }

    protected function setLang($lang)
    {
        \Session::put('locale', $lang);

        return redirect()->refresh();
    }

    protected function index(string $slug, ?string $locale = null)
    {
        if (is_null($locale)) {
            $locale = get_current_lang();
        }

        $modified_slug = $this->modifyTranslatedSlug($locale, $slug);

        if (! is_string($modified_slug)) {
            return $modified_slug;
        }

        $this->data = $this->service->resolveBySlug($modified_slug);

        if (is_null($this->data)) {
            throwNotFound();
        }

        \Session::put('slug', $slug);

        return true;
    }

    protected function modifyTranslatedSlug($locale, $slug)
    {
        if (in_array($locale, $this->lang_prefixes) && $locale !== get_current_lang()) {
            return $this->setLang($locale);
        }

        if (! in_array($locale, $this->lang_prefixes) && $slug === '/') {
            $slug = $locale;
        }

        return $slug;
    }

    protected function languageIndex($locale, string $slug = '/')
    {
        return $this->index($slug, $locale);
    }
}
