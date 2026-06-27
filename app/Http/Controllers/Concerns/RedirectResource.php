<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

trait RedirectResource
{
    /**
     * Build success message
     */
    protected function successMessage(Model $resource, string $action): string
    {
        $model = $this->extractModelName($resource);
        $name = $this->extractName($resource);

        return "{$model} - {$name} has been {$action}.";
    }

    /**
     * Extract the model class name
     */
    protected function extractModelName(Model $resource): string
    {
        if ($this->hasMethod($resource, 'renderModelName')) {
            return $resource->renderModelName();
        }

        return ucwords(Str::replace('_', ' ', Str::snake(class_basename($resource))));
    }

    /**
     * Extract the model resource name
     */
    protected function extractName(Model $item): string
    {
        return match (true) {
            $this->hasMethod($item, 'representation') => $item->representation(),
            ! is_null($item->name) => $item->name,
            ! is_null($item->title) => $item->title,
            ! is_null($item->label) => $item->label,
            default => "Resource #{$item->getKey()}",
        };
    }

    /**
     * Check if the model has the given method
     */
    protected function hasMethod(Model $item, string $method): bool
    {
        return (new ReflectionClass($item))->hasMethod($method);
    }
}
