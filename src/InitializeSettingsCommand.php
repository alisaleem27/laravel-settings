<?php

declare(strict_types=1);

namespace AliSaleem\LaravelSettings;

use Illuminate\Console\Command;
use ReflectionClass;
use ReflectionProperty;

class InitializeSettingsCommand extends Command
{
    protected $signature = 'settings:init';

    protected $description = 'Fill required settings';

    public function handle(): void
    {
        collect((new ReflectionClass(settings()))->getProperties(ReflectionProperty::IS_PUBLIC))
            ->filter(fn (ReflectionProperty $property) => ! $property->isInitialized(settings()))
            ->each(function (ReflectionProperty $property) {
                $name = str($property->getName())
                    ->explode('_')
                    ->map(fn (string $part) => str($part)->headline())
                    ->implode(' | ');
                $answer = null;
                while (is_null($answer)) {
                    if (is_null($answer = $this->ask($name))) {
                        $this->error("'$name' is required");
                    }
                }
                $property->setValue(settings(), $answer);
            });
    }
}
