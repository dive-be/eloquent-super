<?php declare(strict_types=1);

namespace Dive\EloquentSuper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait InheritsFromSuper
{
    abstract protected function getSuperClass(): string;

    protected function initializeInheritsFromSuper(): void
    {
        $this->with[] = 'super';
    }

    public function super(): MorphOne
    {
        return $this->morphOne($this->getSuperClass(), $this->superName())->withDefault();
    }

    public function delete(): ?bool
    {
        if ($this->usesSoftDeletes()) {
            return parent::delete();
        }

        return $this
            ->getConnection()
            ->transaction(fn () => $this->getRelationValue('super')->delete() && parent::delete());
    }

    public function fill(array $attributes): static
    {
        [$super, $sub] = $this->partitionAttributes($attributes);

        parent::fill($sub);
        $this->getRelationValue('super')->fill($super);

        return $this;
    }

    public function save(array $options = []): bool
    {
        return $this->getConnection()->transaction(function () use ($options) {
            parent::save($options);

            return $this
                ->getRelationValue('super')
                ->setAttribute($this->super()->getForeignKeyName(), $this->getKey())
                ->save($options);
        });
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        [$super, $sub] = $this->partitionAttributes($attributes);

        return $this
            ->getConnection()
            ->transaction(fn () => $this->getRelationValue('super')->update($super, $options) && parent::update($sub, $options));
    }

    public function setCreatedAt($value): static
    {
        $this->setAttribute($this->getCreatedAtColumn(), $value);
        $this->getRelationValue('super')->setCreatedAt($value);

        return $this;
    }

    public function setUpdatedAt($value): static
    {
        $this->setAttribute($this->getUpdatedAtColumn(), $value);
        $this->getRelationValue('super')->setUpdatedAt($value);

        return $this;
    }

    protected function superName(): string
    {
        return Str::of($this->getSuperClass())
            ->classBasename()
            ->snake()
            ->append('able')
            ->value();
    }

    private function usesSoftDeletes(): bool
    {
        return method_exists($this, 'runSoftDelete');
    }

    private function partitionAttributes(array $attributes): array
    {
        $superAttributes = $this->newSuper()->getFillable();

        return Collection::make($attributes)
            ->partition(static fn ($_, $attribute) => in_array($attribute, $superAttributes))
            ->toArray();
    }

    private function newSuper(): Model
    {
        return new ($this->getSuperClass());
    }

    public function __call($method, $parameters): mixed
    {
        if (! method_exists($super = $this->getRelationValue('super'), $method)) {
            return parent::__call($method, $parameters);
        }

        return $super->{$method}(...$parameters);
    }

    public function __get($key): mixed
    {
        if (! is_null($value = parent::__get($key))) {
            return $value;
        }

        if (! $this->relationLoaded('super')) {
            return $value;
        }

        return $this->getRelationValue('super')->__get($key);
    }

    public function __set($key, $value): void
    {
        if ($this->newSuper()->isFillable($key)) {
            $this->getRelationValue('super')->__set($key, $value);
        } else {
            parent::__set($key, $value);
        }
    }
}
