<?php

namespace App\Services\Concerns;

use App\Const\RequestFilter;
use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

trait HasListProcessor
{
    /**
     * Base query to be applied to the builder
     */
    protected ?Closure $whereQuery = null;

    /**
     * The index query builder
     */
    protected Builder $listQueryBuilder;

    /**
     * List of eager loaded relationships
     *
     * @var array<mixed>
     */
    protected array $eagerLoads = [];

    /**
     * Default order by column and direction
     */
    protected ?string $orderByColumn = null;

    protected ?string $orderByDirection = null;

    /**
     * The index pagination
     */
    protected LengthAwarePaginator $indexPagination;

    /**
     * Determines if list should be paginated
     */
    protected bool $paginated = true;

    /**
     * Display a listing of the resource
     */
    abstract public function index(Request $request, array $additionalProps = []): Response;

    /**
     * Set list of eager loaded relationships
     */
    protected function with(array|string $relations): self
    {
        $this->eagerLoads = is_string($relations) ? func_get_args() : $relations;

        return $this;
    }

    /**
     * Set the base query
     */
    public function where(Closure $query): self
    {
        $this->whereQuery = $query;

        return $this;
    }

    /**
     * Set default order by
     */
    public function defaultOrderBy(string $column, string $direction = 'desc'): self
    {
        $this->orderByColumn = $column;
        $this->orderByDirection = $direction;

        return $this;
    }

    /**
     * Default index
     */
    public function defaultIndex(Request $request, array $additionalProps = []): Response
    {
        $this->initQueries();

        $data = [];

        $instance = $this->setIndexQuery($request);

        if ($this->paginated) {
            $instance->paginateIndexData($request);
        }

        $data = $instance->getIndexData($request);

        return $this->renderIndex($request, array_merge($additionalProps, [
            'data' => $data,
        ]));
    }

    /**
     * Paginate the index data
     */
    public function paginateIndexData(Request $request): self
    {
        $items = $this->listQueryBuilder
            ->paginate((int) $request->input(RequestFilter::PAGINATION_KEY, RequestFilter::PAGINATION_LIMIT))
            ->appends($request->query());

        $this->indexPagination = $items;

        return $this;
    }

    /**
     * Render index
     */
    public function renderIndex(Request $request, array $additionalProps = []): Response
    {
        return Inertia::render($this->pageComponent, array_merge(
            $request->query(),
            $additionalProps,
        ));
    }

    /**
     * Set the index query for listing the resource
     */
    public function setIndexQuery(Request $request): self
    {
        $query = match (true) {
            $this->model instanceof Relation => $this->model->newQuery(),
            $this->model instanceof EloquentBuilder => $this->model->newQuery(),
            default => $this->model->query(),
        };
        if ($this->whereQuery instanceof Closure) {
            $query = ($this->whereQuery)($query);
        }

        $this->listQueryBuilder = $query
            ->with($this->eagerLoads)
            ->when($this->orderByColumn, fn ($q) => $q->orderBy($this->orderByColumn, $this->orderByDirection));

        return $this;
    }

    /**
     * Get the index data
     */
    public function getIndexData(Request $request): mixed
    {
        $items = $this->paginated ? $this->indexPagination : $this->listQueryBuilder->get();

        if ($this->jsonResourceClass) {
            $items = $this->jsonResourceClass::collection($items);

            if ($this->paginated) {
                $responseData = $items->response()->getData();

                // Transform stdClass to array and flatten pagination meta to top level
                $items = json_decode(json_encode($responseData), true);

                if ($this->paginated && isset($items['meta'])) {
                    $items = array_merge($items, $items['meta']);
                    unset($items['meta'], $items['links']);
                }
            }
        }

        return $items;
    }
}
