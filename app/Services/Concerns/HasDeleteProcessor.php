<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasDeleteProcessor
{
    /**
     * Remove the specified resource from storage
     */
    abstract public function delete(Request $request, bool $forceDelete = false): Model;

    /**
     * Default delete
     */
    public function defaultDelete(Request $request, bool $forceDelete = false): Model
    {
        match ($forceDelete) {
            true => $this->model->forceDelete(),
            default => $this->model->delete(),
        };

        return $this->model;
    }
}
