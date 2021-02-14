<?php

namespace Tests\Feature;

use function Pest\Laravel\assertDatabaseCount;
use Tests\Fakes\SubModel;

test('super is automatically destroyed if sub initiates a destructive operation', function () {
    seed();

    assertDatabaseCount($super = 'test_models_super', 1);
    assertDatabaseCount($sub = 'test_models_sub', 1);

    SubModel::query()->first()->delete();

    assertDatabaseCount($super, 0);
    assertDatabaseCount($sub, 0);
});
