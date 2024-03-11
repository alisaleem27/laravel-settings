<?php

declare(strict_types=1);

namespace AliSaleem\LaravelSettings;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

abstract class Settings
{
    protected Valuestore $store;

    protected array $original = [];

    public function __construct(protected array $config)
    {
        $this->store = Valuestore::make(data_get($config, 'storage.path'))->setDisk(data_get($config, 'storage.disk'));

        collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property) => $property->isInitialized($this))
            ->map(fn (ReflectionProperty $property) => $property->getName())
            ->each(function ($property) {
                $this->original[$property] = $this->$property;
            });

        collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->keyBy(fn (ReflectionProperty $property) => $property->getName())
            ->map(fn (ReflectionProperty $property) => $property->getType()->getName())
            ->filter(fn ($type, $property) => $this->store->has($property))
            ->each(function ($type, $property) {
                $value = $this->hydrate($this->store->get($property), $type);
                $this->$property = $value;
                $this->original[$property] = $value;
            });
    }

    public function __destruct()
    {
        $this->persist();
    }

    public function persist(): void
    {
        collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property) => $property->isInitialized($this))
            ->keyBy(fn (ReflectionProperty $property) => $property->getName())
            ->map(fn (ReflectionProperty $property) => $property->getType()->getName())
            ->filter(fn ($type, $property) => array_key_exists($property, $this->original) && $this->original[$property] !== $this->$property)
            ->each(function ($type, $property) {
                $this->store->put($property, $this->dehydrate($this->$property, $type));
                $this->original[$property] = $this->$property;
            });
    }

    protected function hydrate($value, string $type): array|bool|float|int|string|Collection|null
    {
        return match ($type) {
            'array' => json_decode($value, true),
            Collection::class => collect(json_decode($value, true)),
            default => $value,
        };
    }

    protected function dehydrate($value, string $type): bool|float|int|string|null
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'array' => json_encode($value),
            Collection::class => $value->toJson(),
            default => $value,
        };
    }
}
