<?php declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Fakes\SoftModel;
use Tests\Fakes\SubModel;

uses(Tests\TestCase::class)->in(__DIR__);

function seed(): void
{
    DB::table('test_models_sub')->insert([
        'gender' => 'm',
        'email' => 'me@email.com',
        'created_at' => ($now = Carbon::now()->toDateTimeString()),
        'updated_at' => $now,
    ]);

    DB::table('test_models_super')->insert([
        'super_modelable_type' => SubModel::class,
        'super_modelable_id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

function seedSoftDeletes(): void
{
    DB::table('test_models_soft')->insert([
        'gender' => 'm',
        'email' => 'me@email.com',
        'created_at' => ($now = Carbon::now()->toDateTimeString()),
        'updated_at' => $now,
    ]);

    DB::table('test_models_super')->insert([
        'super_modelable_type' => SoftModel::class,
        'super_modelable_id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}
