<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class UpdateTest extends TestCase
{
    #[Test]
    public function attributes_are_updated_in_the_right_tables(): void
    {
        SubModel::query()->first()->update(array_merge(
            $sub = ['gender' => 'f'],
            $super = ['first_name' => 'Louis'],
        ));

        $this->assertDatabaseHas('test_models_sub', $sub);
        $this->assertDatabaseHas('test_models_super', $super);
    }
}
