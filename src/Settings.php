<?php

namespace AliSaleem\LaravelSettings;

use AliSaleem\LaravelSettings\Support\Type;
use AliSaleem\LaravelSettings\Support\Valuestore;
use Illuminate\Support\Collection;

class Settings
{
    protected Valuestore $store;

    protected Collection $schema;

    public function __construct()
    {
        $this->store = Valuestore::make(config('settings.storage.path'))->setDisk(config('settings.storage.disk'));
        $this->schema = collect(config('settings.schema'))->keyBy('key');
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function get(string $key): float|int|bool|array|string|null
    {
        $value = $this->store->get($key);

        return $this->hydrate($value, $this->schema->get($key)->type);
    }

    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    public function set(string $key, $value): static
    {
        $this->store->put($key, $this->dehydrate($value, $this->schema->get($key)->type));

        return $this;
    }

    public function __unset(string $name): void
    {
        $this->forget($name);
    }

    public function forget(string $key): static
    {
        $this->store->forget($key);

        return $this;
    }

    protected function hydrate($value, Type $type): float|int|bool|array|string|null
    {
        return match ($type) {
            Type::CSV => $value ? str_getcsv($value) : [],
            default => $value,
        };
    }

    protected function dehydrate($value, Type $type): float|int|bool|string|null
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            Type::Boolean => boolval($value),
            Type::CSV => $value ? $this->toCSV($value) : null,
            Type::Integer => intval($value),
            Type::Float => floatval($value),
            Type::Text => (string) $value,
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
