<?php

namespace App\Services\Concerns;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ResourceService
{
    /**
     * The model instance
     */
    protected Model|Builder $model;

    /**
     * The class to render a JSON representation of the resource
     */
    protected string $jsonResourceClass;

    /**
     * The page component to display the resource
     */
    protected string $pageComponent;

    /**
     * Set model instance
     */
    public function setModel(Model|Builder|string $model): self
    {
        if (is_string($model)) {
            $model = new $model;
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set class to render a JSON representation of the resource
     *
     * @param  string<JsonResource>  $jsonResourceClass
     */
    public function setJsonResource(string $jsonResourceClass): self
    {
        $this->jsonResourceClass = $jsonResourceClass;

        return $this;
    }

    /**
     * Set the page component to display the resource
     */
    public function setPageComponent(string $pageComponent): self
    {
        $this->pageComponent = $pageComponent;

        return $this;
    }

    /**
     * Set default queries (filters, sort, & relationships) for index and exports.
     */
    protected function initQueries(): void
    {
        // Override in the main repo.
    }
}
