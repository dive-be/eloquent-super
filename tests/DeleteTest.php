<?php declare(strict_types=1);

namespace Tests;

use Tests\Fakes\SoftModel;
use Tests\Fakes\SubModel;
use function Pest\Laravel\assertDatabaseCount;

test('super is automatically destroyed if sub initiates a destructive operation', function () {
    seed();

    assertDatabaseCount($super = 'test_models_super', 1);
    assertDatabaseCount($sub = 'test_models_sub', 1);

    SubModel::query()->first()->delete();

    assertDatabaseCount($super, 0);
    assertDatabaseCount($sub, 0);
});

test('sub model is soft deleted and super model is left alone', function () {
    seedSoftDeletes();

    $sub = SoftModel::query()->first();

    expect($sub->trashed())->toBeFalse();

    $sub->delete();
    $sub->refresh();

    expect($sub->trashed())->toBeTrue();
    expect($sub->super->exists)->toBeTrue();
});
