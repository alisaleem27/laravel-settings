<?php

declare(strict_types=1);

return [
    'class' => \AliSaleem\LaravelSettings\BaseSettings::class, // replace with an extension of this class

    'storage' => [
        'disk' => null, // default disk
        'path' => 'settings.json',
    ],

    'logging' => false,
];
