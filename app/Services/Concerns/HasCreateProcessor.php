<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

trait HasCreateProcessor
{
    /**
     * Store a newly created resource in storage
     */
    abstract public function store(FormRequest $request): Model;

    /**
     * Default store
     */
    public function defaultStore(FormRequest $request): Model
    {
        if (is_string($this->model)) {
            return $this->model::create($this->prepareStoreData($request));
        }

        return $this->model->create($this->prepareStoreData($request));
    }

    /**
     * Prepare store data
     */
    public function prepareStoreData(FormRequest $request): array
    {
        return $request->validated();
    }
}
