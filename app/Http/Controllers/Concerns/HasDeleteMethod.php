<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasDeleteMethod
{
    /**
     * Default delete
     */
    public function defaultDelete(Request $request, Model $model, array $redirectParams = [], bool $forceDelete = false): RedirectResponse
    {
        $model = $this->service()->setModel($model)->delete($request, $forceDelete);
        $message = $this->successMessage($model, $forceDelete ? 'deleted' : 'archived');

        return redirect()->back()->with('success', $message);
    }
}
