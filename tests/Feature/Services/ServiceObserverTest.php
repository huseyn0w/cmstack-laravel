<?php

use App\Http\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sanitizes script tags out of content on save', function () {
    $service = Service::create(['sort_order' => 0]);
    $t = $service->translateOrNew('en');
    $t->title = 'XSS';
    $t->slug = 'xss';
    $t->content = '<p>ok</p><script>alert(1)</script>';
    $t->status = Service::STATUS_PUBLISHED;
    $service->save();

    expect($service->fresh()->translate('en')->content)->not->toContain('<script>');
});

it('sanitizes script tags out of the excerpt on save', function () {
    $service = Service::create(['sort_order' => 0]);
    $t = $service->translateOrNew('en');
    $t->title = 'XSS2';
    $t->slug = 'xss2';
    $t->excerpt = 'lead <script>alert(2)</script>';
    $t->status = Service::STATUS_PUBLISHED;
    $service->save();

    expect($service->fresh()->translate('en')->excerpt)->not->toContain('<script>');
});
