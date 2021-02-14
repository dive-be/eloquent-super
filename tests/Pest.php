<?php

use Tests\Fakes\SubModel;

uses(Tests\TestCase::class)->in('Feature');

function seed()
{
    resolve('db')->table('test_models_sub')->insert([
        'gender' => 'm',
        'email' => 'me@email.com',
        'created_at' => ($now = now()->toDateTimeString()),
        'updated_at' => $now,
    ]);

    resolve('db')->table('test_models_super')->insert([
        'super_modelable_type' => SubModel::class,
        'super_modelable_id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}
