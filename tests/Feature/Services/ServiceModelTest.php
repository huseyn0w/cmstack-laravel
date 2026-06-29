<?php

// tests/Feature/Services/ServiceModelTest.php

use App\Http\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('persists a translatable service with a translation row', function () {
    $service = Service::create(['sort_order' => 5]);
    $service->translateOrNew('en')->title = 'Web Design';
    $service->translateOrNew('en')->slug = 'web-design';
    $service->translateOrNew('en')->status = Service::STATUS_PUBLISHED;
    $service->save();

    $fresh = Service::with('translations')->find($service->id);
    expect($fresh->sort_order)->toBe(5)
        ->and($fresh->translate('en')->title)->toBe('Web Design')
        ->and($fresh->translate('en')->slug)->toBe('web-design')
        ->and((int) $fresh->translate('en')->status)->toBe(1);
});

it('soft-deletes a service', function () {
    $service = Service::create(['sort_order' => 0]);
    $service->delete();
    expect(Service::find($service->id))->toBeNull()
        ->and(Service::withTrashed()->find($service->id))->not->toBeNull();
});
