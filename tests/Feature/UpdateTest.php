<?php

namespace Tests\Feature;

use function Pest\Laravel\assertDatabaseHas;
use Tests\Fakes\SubModel;

test('attributes are updated in the right tables', function () {
    seed();

    SubModel::query()->first()->update(array_merge(
        $sub = ['gender' => 'f'],
        $super = ['first_name' => 'Louis'],
    ));

    assertDatabaseHas('test_models_sub', $sub);
    assertDatabaseHas('test_models_super', $super);
});
