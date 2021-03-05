<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Tests\Fakes\SubModel;

test('super relationship exists', function () {
    $relationship = (new SubModel())->super();

    expect($relationship)->toBeInstanceOf(MorphOne::class);
    expect($relationship->getForeignKeyName())->toBe('super_modelable_id');
    expect($relationship->getMorphType())->toBe('super_modelable_type');
});

test('super relationship is always eager loaded', function () {
    seed();

    $model = SubModel::query()->first();

    expect($model->relationLoaded('super'))->toBeTrue();
});
