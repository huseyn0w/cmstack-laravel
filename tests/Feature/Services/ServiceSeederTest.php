<?php

use App\Http\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds three published sample services with en + ru translations', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Service::count())->toBe(3);

    $first = Service::ordered()->first();
    expect($first->translate('en')->title)->not->toBeEmpty()
        ->and($first->translate('ru')->title)->not->toBeEmpty()
        ->and((int) $first->translate('en')->status)->toBe(1);
});
