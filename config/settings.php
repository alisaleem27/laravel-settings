<?php

return [
    'provider' => \AliSaleem\LaravelSettings\Settings::class,

    'storage' => [
        'disk' => null,
        'path' => 'settings.json',
    ],

    'schema' => [
        // new \AliSaleem\LaravelSettings\Field('name'),
    ],
];
