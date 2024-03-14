<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\SubModel;

final class RelationshipTest extends TestCase
{
    #[Test]
    public function super_relationship_exists(): void
    {
        $relationship = (new SubModel())->super();

        $this->assertInstanceOf(MorphOne::class, $relationship);
        $this->assertSame('super_modelable_id', $relationship->getForeignKeyName());
        $this->assertSame('super_modelable_type', $relationship->getMorphType());
    }

    #[Test]
    public function super_relationship_is_always_eagerly_loaded(): void
    {
        $model = SubModel::query()->first();

        $this->assertTrue($model->relationLoaded('super'));
    }
}
