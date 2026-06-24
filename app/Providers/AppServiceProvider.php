<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Phase 4: the public theme is Tailwind. Use Laravel's Tailwind
        // paginator so ->links() (and the pretty_url()/pretty_search_url()
        // helpers that wrap it) emit Tailwind markup instead of Bootstrap.
        Paginator::useTailwind();

        // The MCP server authenticates AI clients (e.g. Claude) over OAuth 2.1
        // via Passport. This is the consent screen shown to a logged-in admin
        // when an MCP client requests authorization — see resources/views/mcp/.
        Passport::authorizationView(fn ($parameters) => view('mcp.authorize', $parameters));

        // Access tokens issued to MCP clients are long-lived enough to be
        // practical but still expire; refresh tokens keep the connection alive.
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        view()->composer('*', function ($view) {
            $view->with('current_user', \Auth::user());
            $view->with('home_page_data', get_data(1, 'page', ['slug', 'title']));
        });
    }
}
