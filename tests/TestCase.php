<?php

namespace Tests;

use Dive\EloquentSuper\EloquentSuperServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('eloquent-super.relationship', 'super');
    }

    protected function getPackageProviders($app)
    {
        return [EloquentSuperServiceProvider::class];
    }

    protected function setUpDatabase($app)
    {
        $schema = $app->get('db')->connection()->getSchemaBuilder();

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
    }
}
