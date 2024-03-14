<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class TimestampsTest extends TestCase
{
    #[Test]
    public function created_at_is_set_for_super(): void
    {
        $model = new SubModel();

        $model->setCreatedAt($now = now());

        $this->assertEqualsWithDelta($now, $model->created_at, 1);
        $this->assertEqualsWithDelta($now, $model->super->created_at, 1);
    }

    #[Test]
    public function updated_at_is_set_for_super(): void
    {
        $model = new SubModel();

        $model->setUpdatedAt($now = now());

        $this->assertEqualsWithDelta($now, $model->updated_at, 1);
        $this->assertEqualsWithDelta($now, $model->super->updated_at, 1);
    }
}
