<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

trait HasCreateMethod
{
    /**
     * Default create
     */
    public function defaultCreate(Request $request, array $props = []): Response
    {
        return Inertia::render("{$this->directory()}/Create", array_merge(
            $request->query(),
            $props
        ));
    }

    /**
     * Default store
     */
    public function defaultStore(FormRequest $request, Model|Relation|string $model, array $redirectParams = []): RedirectResponse
    {
        $resource = $this->service()->setModel($model)->store($request);
        $message = $this->successMessage($resource, 'created');

        return to_route("{$this->routeName()}.index", $redirectParams)->with('success', $message);
    }
}
