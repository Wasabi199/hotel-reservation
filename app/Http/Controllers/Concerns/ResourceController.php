<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Controllers\Controller;
use App\Services\Concerns\ResourceService;

abstract class ResourceController extends Controller
{
    use RedirectResource;

    /**
     * The inertia base directory for the resource
     *
     * @example 'Admin/Accounts/Admin'
     */
    abstract protected function directory(): string;

    /**
     * The base route name for the resource
     *
     * @example 'admin.accounts.admins'
     */
    abstract protected function routeName(): string;

    /**
     * The base service for the resource
     */
    abstract protected function service(): ResourceService;
}
