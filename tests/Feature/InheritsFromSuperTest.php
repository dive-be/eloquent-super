<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use Tests\Fakes\SubModel;
use Tests\Fakes\SuperModel;

it('defines a super relationship and provides a default', function () {
    $relationship = (new SubModel())->super();

    expect($relationship)->toBeInstanceOf(MorphOne::class);
    expect($relationship->getForeignKeyName())->toBe('super_modelable_id');
    expect($relationship->getMorphType())->toBe('super_modelable_type');
});

it('will always eager load the super relationship', function () {
    $model = new SubModel();

    expect($model->getWith())->toBe(['super']);
});

it('automatically deletes the super model if sub model initiates a destruction operation', function () {
    seed();

    assertDatabaseCount($super = (new SuperModel())->getTable(), 1);
    assertDatabaseCount($sub = (new SubModel())->getTable(), 1);

    SubModel::first()->delete();

    assertDatabaseCount($super, 0);
    assertDatabaseCount($sub, 0);
});

it('fills the attributes in the right models', function () {
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

it('saves the attributes in the right tables', function () {
    $model = new SubModel(array_merge(
        $super = ['first_name' => 'bob', 'last_name' => 'richards'],
        $sub = ['gender' => 'm', 'email' => 'bob@mail.co.uk'],
    ));

    $model->save();

    assertDatabaseHas((new SubModel())->getTable(), $sub);
    assertDatabaseHas((new SuperModel())->getTable(), $super);
});

it('updates the right tables', function () {
    seed();

    SubModel::first()->update(array_merge(
        $sub = ['gender' => 'f'],
        $super = ['first_name' => 'Louis'],
    ));

    assertDatabaseHas((new SubModel())->getTable(), $sub);
    assertDatabaseHas((new SuperModel())->getTable(), $super);
});

it('sets created_at for super as well', function () {
    $model = new SubModel();

    $model->setCreatedAt($now = now());

    expect($model->created_at)->toEqualWithDelta($now, 1);
    expect($model->super->created_at)->toEqualWithDelta($now, 1);
});

it('sets updated_at for super as well', function () {
    $model = new SubModel();

    $model->setUpdatedAt($now = now());

    expect($model->updated_at)->toEqualWithDelta($now, 1);
    expect($model->super->updated_at)->toEqualWithDelta($now, 1);
});

it('automatically retrieves attribute from super', function () {
    seed();
    $model = SubModel::first();

    $firstName = $model->first_name;

    expect($model->getAttribute('first_name'))->toBeNull();
    expect($firstName)->toBe($model->super->first_name);
});

it('automatically sets attribute on super', function () {
    $model = new SubModel();

    $model->first_name = 'William';

    expect($model->getAttribute('first_name'))->toBeNull();
    expect($model->super->first_name)->toBe('William');
});

it('automatically calls method on super', function () {
    $model = new SubModel();

    $result = $model->aRandomMethod();

    expect(method_exists($model, 'aRandomMethod'))->toBeFalse();
    expect($result)->toBe('Lorem');
});

function seed()
{
    DB::table((new SubModel())->getTable())->insert([
        'gender' => 'm',
        'email' => 'me@email.com',
        'created_at' => ($now = now()->toDateTimeString()),
        'updated_at' => $now,
    ]);

    DB::table((new SuperModel())->getTable())->insert([
        'super_modelable_type' => SubModel::class,
        'super_modelable_id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}
