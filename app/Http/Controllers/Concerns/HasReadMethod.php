<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Response;

trait HasReadMethod
{
    /**
     * Default show
     *
     * @param  string<JsonResource>  $jsonResourceClass
     */
    public function defaultShow(Request $request, Model|Relation $model, string $jsonResourceClass, array $props = []): Response
    {
        return $this->service()
            ->setModel($model)
            ->setJsonResource($jsonResourceClass)
            ->setPageComponent("{$this->directory()}/Show")
            ->show($request, $props);
    }
}
