<?php declare(strict_types=1);

namespace Tests;

use Tests\Fakes\SubModel;

test('attributes are updated in the right tables', function () {
    seed();

    SubModel::query()->first()->update(array_merge(
        $sub = ['gender' => 'f'],
        $super = ['first_name' => 'Louis'],
    ));

    $this->assertDatabaseHas('test_models_sub', $sub);
    $this->assertDatabaseHas('test_models_super', $super);
});
