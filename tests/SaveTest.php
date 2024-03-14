<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class SaveTest extends TestCase
{
    #[Test]
    public function attributes_are_saved_in_the_right_tables(): void
    {
        $model = new SubModel(array_merge(
            $super = ['first_name' => 'bob', 'last_name' => 'richards'],
            $sub = ['gender' => 'm', 'email' => 'bob@mail.co.uk'],
        ));

        $model->save();

        $this->assertDatabaseHas('test_models_sub', $sub);
        $this->assertDatabaseHas('test_models_super', $super);
    }
}
