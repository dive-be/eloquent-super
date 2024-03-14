<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Fakes\SoftModel;
use Tests\Fakes\SubModel;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase($app): void
    {
        $db = $app['db'];
        $schema = $db->connection()->getSchemaBuilder();

        $schema->create('test_models_super', static function (Blueprint $table) {
            $table->id();
            $table->morphs('super_modelable');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });

        $schema->create('test_models_sub', static function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('email');
            $table->timestamps();
        });

        $schema->create('test_models_soft', static function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('email');
            $table->softDeletes();
            $table->timestamps();
        });

        $db->table('test_models_sub')->insert([
            'gender' => 'm',
            'email' => 'me@email.com',
            'created_at' => ($now = Carbon::now()->toDateTimeString()),
            'updated_at' => $now,
        ]);

        $db->table('test_models_super')->insert([
            'super_modelable_type' => SubModel::class,
            'super_modelable_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('test_models_soft')->insert([
            'gender' => 'm',
            'email' => 'me@email.com',
            'created_at' => ($now = Carbon::now()->toDateTimeString()),
            'updated_at' => $now,
        ]);

        $db->table('test_models_super')->insert([
            'super_modelable_type' => SoftModel::class,
            'super_modelable_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Model::unguard();
    }
}
