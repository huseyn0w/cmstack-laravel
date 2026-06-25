<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case binding
|--------------------------------------------------------------------------
| Pest function-style tests in Feature/ and Unit/ resolve against the
| project TestCase (which pins SQLite :memory: via CreatesApplication).
| Existing class-based PHPUnit tests are unaffected — they already extend
| Tests\TestCase directly.
*/
uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/
expect()->extend('toBeOne', fn () => $this->toBe(1));
