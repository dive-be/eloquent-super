<?php

namespace Dive\EloquentSuper;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait InheritsFromSuper
{
    protected function initializeInheritsFromSuper()
    {
        $this->with[] = config('eloquent-super.relationship');
    }

    public function super(): MorphOne
    {
        return $this->morphOne(
            $this->getSuperClass(),
            Str::snake(class_basename($this->getSuperClass())).'able'
        )->withDefault();
    }

    public function delete()
    {
        return DB::transaction(fn () => parent::delete()
            && $this->getRelationValue(config('eloquent-super.relationship'))->delete());
    }

    public function fill(array $attributes)
    {
        [$super, $sub] = $this->partitionAttributes($attributes);

        parent::fill($sub);
        $this->getRelationValue(config('eloquent-super.relationship'))->fill($super);

        return $this;
    }

    public function save(array $options = [])
    {
        return DB::transaction(fn () => parent::save($options)
            && $this->getRelationValue(config('eloquent-super.relationship'))
                ->setAttribute($this->super()->getForeignKeyName(), $this->getKey())
                ->save($options));
    }

    public function update(array $attributes = [], array $options = [])
    {
        [$super, $sub] = $this->partitionAttributes($attributes);

        return DB::transaction(fn () => parent::update($sub, $options)
            && $this->getRelationValue(config('eloquent-super.relationship'))->update($super, $options));
    }

    public function setCreatedAt($value)
    {
        $this->setAttribute($column = $this->getCreatedAtColumn(), $value);
        $this->getRelationValue(config('eloquent-super.relationship'))->setAttribute($column, $value);

        return $this;
    }

    public function setUpdatedAt($value)
    {
        $this->setAttribute($column = $this->getUpdatedAtColumn(), $value);
        $this->getRelationValue(config('eloquent-super.relationship'))->setAttribute($column, $value);

        return $this;
    }

    abstract protected function getSuperClass(): string;

    private function partitionAttributes(array $attributes): array
    {
        return collect($attributes)
            ->partition(fn ($_, $attribute) => in_array($attribute, $this->newSuper()->getFillable()))
            ->toArray();
    }

    private function newSuper()
    {
        $super = $this->getSuperClass();

        return new $super();
    }

    public function __call($method, $parameters)
    {
        if (! method_exists($super = $this->getRelationValue(config('eloquent-super.relationship')), $method)) {
            return parent::__call($method, $parameters);
        }

        return $super->{$method}(...$parameters);
    }

    public function __get($key)
    {
        if (! is_null($value = parent::__get($key))) {
            return $value;
        }

        if (! $this->relationLoaded(config('eloquent-super.relationship'))) {
            return $value;
        }

        return $this->getRelationValue(config('eloquent-super.relationship'))->__get($key);
    }

    public function __set($key, $value)
    {
        if ($this->newSuper()->isFillable($key)) {
            $this->getRelationValue(config('eloquent-super.relationship'))->__set($key, $value);
        } else {
            parent::__set($key, $value);
        }
    }
}
