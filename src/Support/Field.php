<?php

namespace AliSaleem\LaravelSettings\Support;

readonly class Field
{
    public function __construct(
        public string $key,
        public Type $type = Type::Text,
    ) {
        throw_if(
            preg_replace('/[a-zA-Z0-9_]/', '', $key),
            new \InvalidArgumentException("Settings key [{$key}] is invalid. Keys can only contain a-z, A-Z, 0-9, _")
        );
    }
}
