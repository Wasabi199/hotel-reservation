<?php

namespace App\Services\Concerns;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

trait HasReadProcessor
{
    /**
     * Show a specific resource
     */
    abstract public function show(Request $request, array $additionalProps = []): Response;

    /**
     * Default show
     */
    public function defaultShow(Request $request, array $additionalProps = []): Response
    {
        $additionalProps['resource'] = $this->jsonResourceClass::make($this->model)->resolve();

        return $this->renderShow($request, $additionalProps);
    }

    /**
     * Render show
     */
    public function renderShow(Request $request, array $additionalProps = []): Response
    {
        return Inertia::render($this->pageComponent, array_merge(
            $request->query(),
            $additionalProps,
        ));
    }
}
