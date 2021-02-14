<?php

namespace Tests\Feature;

use Tests\Fakes\SubModel;

test('attributes are assigned to the right models', function () {
    $model = new SubModel();

    $model->fill(array_merge(
        $super = ['first_name' => 'bob', 'last_name' => 'richards'],
        $sub = ['gender' => 'm', 'email' => 'bob@mail.co.uk'],
    ));

    expect($model->getAttributes())->toBe($sub);
    expect($model->super->getAttributes())->toBe([
        'super_modelable_id' => null,
        'super_modelable_type' => SubModel::class,
    ] + $super);
});
