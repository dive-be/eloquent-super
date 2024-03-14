<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SoftModel;
use Tests\Fakes\SubModel;

final class DeleteTest extends TestCase
{
    #[Test]
    public function super_is_automatically_destroyed_if_sub_initiates_a_destructive_operation(): void
    {
        $this->assertDatabaseCount($super = 'test_models_super', 2);
        $this->assertDatabaseCount($sub = 'test_models_sub', 1);

        SubModel::query()->first()->delete();

        $this->assertDatabaseCount($super, 1);
        $this->assertDatabaseCount($sub, 0);
    }

    #[Test]
    public function sub_model_is_soft_deleted_and_super_model_is_left_alone(): void
    {
        $sub = SoftModel::query()->first();

        $this->assertFalse($sub->trashed());

        $sub->delete();
        $sub->refresh();

        $this->assertTrue($sub->trashed());
        $this->assertTrue($sub->super->exists);
    }
}
