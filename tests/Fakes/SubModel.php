<?php declare(strict_types=1);

namespace Tests\Fakes;

use Dive\EloquentSuper\InheritsFromSuper;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin SuperModel
 */
class SubModel extends Model
{
    use InheritsFromSuper;

    protected $guarded = [];

    protected $table = 'test_models_sub';

    protected function getSuperClass(): string
    {
        return SuperModel::class;
    }
}
