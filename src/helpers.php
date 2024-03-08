<?php

use AliSaleem\LaravelSettings\Settings;

if (! function_exists('settings')) {
    function settings(): Settings
    {
        return resolve(Settings::class);
    }
}
