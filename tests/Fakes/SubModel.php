<?php

namespace Tests\Fakes;

use Dive\EloquentSuper\InheritsFromSuper;
use Illuminate\Database\Eloquent\Model;

class SubModel extends Model
{
    use InheritsFromSuper;

    protected $guarded = [];

    protected $table = 'test_models_sub';

    public function getWith(): array
    {
        return $this->with;
    }

    protected function getSuperClass(): string
    {
        return SuperModel::class;
    }
}
