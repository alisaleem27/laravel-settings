<?php

namespace AliSaleem\LaravelSettings\Support;

use Illuminate\Support\Facades\Storage;

class Valuestore extends \Spatie\Valuestore\Valuestore
{
    protected ?string $disk;

    public function setDisk(?string $disk = null): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function all(): array
    {
        if (! $this->storage()->exists($this->fileName)) {
            return [];
        }

        return json_decode($this->storage()->get($this->fileName), true) ?? [];
    }

    protected function setContent(array $values): static
    {
        $this->storage()->put($this->fileName, json_encode($values));

        if (! count($values)) {
            $this->storage()->delete($this->fileName);;
        }

        return $this;
    }

    protected function storage()
    {
        return Storage::when($this->disk, fn($storage) => $storage->disk($this->disk));
    }
}