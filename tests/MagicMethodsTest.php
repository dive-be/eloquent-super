<?php declare(strict_types=1);

namespace Tests;

use Tests\Fakes\SubModel;

test('retrieves attribute from super if exists', function () {
    seed();
    $model = SubModel::query()->first();

    $firstName = $model->first_name;

    expect($model->getAttribute('first_name'))->toBeNull();
    expect($firstName)->toBe($model->super->first_name);
});

test('sets attribute on super if it belongs to the super class', function () {
    $model = new SubModel();

    $model->first_name = 'William';

    expect($model->getAttribute('first_name'))->toBeNull();
    expect($model->super->first_name)->toBe('William');
});

test('calls method on super if exists', function () {
    $model = new SubModel();

    $result = $model->aRandomMethod();

    expect(method_exists($model, 'aRandomMethod'))->toBeFalse();
    expect($result)->toBe('Lorem');
});
