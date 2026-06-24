<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Browser (Dusk) tests run the app over real HTTP against a dedicated
        // MySQL database (cmstack_laravel_dusk, see .env.dusk.local). The test process
        // and the served app must SHARE that database, so the in-memory SQLite
        // pin below must NOT apply for Dusk — DUSK=true signals that.
        if (env('DUSK')) {
            return $app;
        }

        // SAFETY: pin the test suite to an isolated in-memory SQLite database,
        // regardless of where env vars come from. Inside Docker the container
        // injects DB_CONNECTION=mysql into $_SERVER, which Laravel's env
        // repository reads before PHPUnit's <env>/.env.testing overrides — so
        // without this the suite would run against (and wipe) the live MySQL
        // dev database. Forcing it here in code is source-independent.
        // Pin the app environment to "testing" so framework test behaviours
        // (e.g. VerifyCsrfToken skipping via runningUnitTests()) kick in even
        // inside Docker, where the container injects APP_ENV=local via $_SERVER.
        $app['env'] = 'testing';

        $config = $app->make('config');
        $config->set('app.env', 'testing');
        $config->set('database.default', 'sqlite');
        $config->set('database.connections.sqlite.database', ':memory:');
        // Pin volatile stores to array and disable the model cache so tests get
        // fresh reads (the Docker container otherwise injects CACHE_DRIVER=file
        // via $_SERVER, leaving genealabs/model-caching serving stale rows).
        $config->set('cache.default', 'array');
        $config->set('session.driver', 'array');
        $config->set('queue.default', 'sync');
        $config->set('laravel-model-caching.enabled', false);

        return $app;
    }
}
