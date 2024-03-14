<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Dive\EloquentSuper\InheritsFromSuper;
use Illuminate\Database\Eloquent\Model;

/** @mixin SuperModel */
final class SubModel extends Model
{
    use InheritsFromSuper;

    protected $table = 'test_models_sub';

    protected function getSuperClass(): string
    {
        return SuperModel::class;
    }
}
