<?php

namespace Tests\Feature;

use Tests\Fakes\SubModel;
use function Pest\Laravel\assertDatabaseHas;

test('attributes are updated in the right tables', function () {
    seed();

    SubModel::query()->first()->update(array_merge(
        $sub = ['gender' => 'f'],
        $super = ['first_name' => 'Louis'],
    ));

    assertDatabaseHas('test_models_sub', $sub);
    assertDatabaseHas('test_models_super', $super);
});
