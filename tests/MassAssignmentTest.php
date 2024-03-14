<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class MassAssignmentTest extends TestCase
{
    #[Test]
    public function attributes_are_assigned_to_the_right_models(): void
    {
        $model = new SubModel();

        $model->fill(array_merge(
            $super = ['first_name' => 'bob', 'last_name' => 'richards'],
            $sub = ['gender' => 'm', 'email' => 'bob@mail.co.uk'],
        ));

        $this->assertSame($sub, $model->getAttributes());
        $this->assertSame([
            'super_modelable_id' => null,
            'super_modelable_type' => SubModel::class,
        ] + $super, $model->super->getAttributes());
    }
}
