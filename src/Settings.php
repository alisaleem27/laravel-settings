<?php

namespace AliSaleem\LaravelSettings;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

abstract class Settings
{
    protected Valuestore $store;
    protected Collection $schema;

    public function __construct(protected array $config)
    {
        $this->store = Valuestore::make(data_get($config, 'storage.path'))->setDisk(data_get($config, 'storage.disk'));

        $this->schema = collect((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->keyBy(fn (ReflectionProperty $property) => $property->getName())
            ->map(fn (ReflectionProperty $property) => $property->getType()->getName());

        $this->schema
            ->filter(fn ($type, $property) => $this->store->has($property))
            ->each(function ($type, $property) {
                $this->$property = $this->hydrate($this->store->get($property), $type);
            });
    }

    public function __destruct()
    {
        $this->persist();
    }

    public function persist(): void
    {
        $this->schema->each(function ($type, $property) {
            $this->store->put($property, $this->dehydrate($this->$property, $this->schema->get($property)));
        });
    }

    protected function hydrate($value, string $type): array|bool|float|int|string|null
    {
        return match ($type) {
            'array' => $value ? str_getcsv($value) : [],
            default   => $value,
        };
    }

    protected function dehydrate($value, string $type): bool|float|int|string|null
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'array'  => $value ? $this->toCSV($value) : null,
            'bool'   => boolval($value),
            'float'  => floatval($value),
            'int'    => intval($value),
            'string' => (string)$value,
        };
    }

    private function toCSV($array): string
    {
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, $array);
        rewind($fp);
        $data = fread($fp, 1048576);
        fclose($fp);

        return rtrim($data, "\n");
    }
}