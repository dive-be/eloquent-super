<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class MagicMethodsTest extends TestCase
{
    #[Test]
    public function retrieves_attribute_from_super_if_exists(): void
    {
        $model = SubModel::query()->first();

        $firstName = $model->first_name;

        $this->assertNull($model->getAttribute('first_name'));
        $this->assertEquals($model->super->first_name, $firstName);
    }

    #[Test]
    public function sets_attribute_on_super_if_it_belongs_to_the_super_class(): void
    {
        $model = new SubModel();

        $model->first_name = 'William';

        $this->assertNull($model->getAttribute('first_name'));
        $this->assertEquals('William', $model->super->first_name);
    }

    #[Test]
    public function calls_method_on_super_if_exists(): void
    {
        $model = new SubModel();

        $result = $model->aRandomMethod();

        $this->assertFalse(method_exists($model, 'aRandomMethod'));
        $this->assertEquals('Lorem', $result);
    }
}
