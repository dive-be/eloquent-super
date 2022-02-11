<?php declare(strict_types=1);

namespace Tests;

use Tests\Fakes\SubModel;

test('created_at is set for super', function () {
    $model = new SubModel();

    $model->setCreatedAt($now = now());

    expect($model->created_at)->toEqualWithDelta($now, 1);
    expect($model->super->created_at)->toEqualWithDelta($now, 1);
});

test('updated_at is set for super', function () {
    $model = new SubModel();

    $model->setUpdatedAt($now = now());

    expect($model->updated_at)->toEqualWithDelta($now, 1);
    expect($model->super->updated_at)->toEqualWithDelta($now, 1);
});
