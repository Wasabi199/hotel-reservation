<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasRestoreProcessor
{
    /**
     * Restore the specified archived resource from storage
     */
    abstract public function restore(Request $request): Model;

    /**
     * Default restore
     */
    public function defaultRestore(Request $request): Model
    {
        $this->model->restore();

        return $this->model;
    }
}
