<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Response;

trait HasListMethod
{
    /**
     * Default index
     *
     * @param  string<JsonResource>  $jsonResourceClass
     */
    public function defaultIndex(Request $request, Model|Relation|string $model, string $jsonResourceClass, array $props = []): Response
    {
        return $this->service()
            ->setModel($model)
            ->setJsonResource($jsonResourceClass)
            ->setPageComponent("{$this->directory()}/Index")
            ->index($request, $props);
    }
}
