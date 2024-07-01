<?php

declare(strict_types=1);

namespace AliSaleem\LaravelSettings;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseSettings
{
    protected Valuestore $store;

    protected array $original = [];

    protected $logging = false;

    public function __construct(protected array $config)
    {
        $this->store = Valuestore::make(data_get($config, 'storage.path'))->setDisk(data_get($config, 'storage.disk'));
        $this->logging = $this->config['logging'] ?? false;

        collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->each(function (ReflectionProperty $property, string $name) {
                $this->original[$property->getName()] = $property->isInitialized($this)
                    ? $property->getValue($this)
                    : null;
            })
            ->filter(fn (ReflectionProperty $property) => $this->store->has($property->getName()))
            ->each(function (ReflectionProperty $property) {
                $rawValue = $this->store->get($property->getName());
                if ($this->logging) {
                    logger()->info("[Settings] read {$property->getName()}: {$rawValue}");
                }
                $value = $this->hydrate($rawValue, $property->getType()->getName());
                $property->setValue($this, $value);
                $this->original[$property->getName()] = $value;
            });
    }

    public function __destruct()
    {
        $this->persist();
    }

    public function persist(): void
    {
        collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property) => $property->isInitialized($this)
                && $this->original[$property->getName()] !== $property->getValue($this)
            )
            ->each(function (ReflectionProperty $property) {
                $value = $this->dehydrate($property->getValue($this), $property->getType()->getName());
                if ($this->logging) {
                    logger()->info("[Settings] writing {$property->getName()}: {$value}");
                }
                $this->store->put($property->getName(), $value);
                $this->original[$property->getName()] = $property->getValue($this);
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

    public function toArray(): array
    {
        return $this->store->all();
    }
}
