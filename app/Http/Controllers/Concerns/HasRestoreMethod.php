<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait HasRestoreMethod
{
    /**
     * Default restore
     */
    public function defaultRestore(Request $request, Model $model, array $redirectParams = []): RedirectResponse
    {
        $model = $this->service()->setModel($model)->restore($request);
        $message = $this->successMessage($model, 'restored');

        return redirect()->back()->with('success', $message);
    }
}
