<?php declare(strict_types=1);

namespace Tests;

use Tests\Fakes\SubModel;

test('attributes are saved in the right tables', function () {
    $model = new SubModel(array_merge(
        $super = ['first_name' => 'bob', 'last_name' => 'richards'],
        $sub = ['gender' => 'm', 'email' => 'bob@mail.co.uk'],
    ));

    $model->save();

    $this->assertDatabaseHas('test_models_sub', $sub);
    $this->assertDatabaseHas('test_models_super', $super);
});
