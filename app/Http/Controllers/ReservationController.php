<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Concerns\HasCreateMethod;
use App\Http\Controllers\Concerns\HasDeleteMethod;
use App\Http\Controllers\Concerns\HasListMethod;
use App\Http\Controllers\Concerns\HasReadMethod;
use App\Http\Controllers\Concerns\HasRestoreMethod;
use App\Http\Controllers\Concerns\ResourceController;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\Reservations\IndexResource;
use App\Http\Resources\Reservations\ShowResource;
use App\Models\Reservation;
use App\Services\Concerns\ResourceService;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ReservationController extends ResourceController
{
    use HasCreateMethod;
    use HasDeleteMethod;
    use HasListMethod;
    use HasReadMethod;
    use HasRestoreMethod;

    /**
     * Inertia Directory
     */
    protected function directory(): string
    {
        return 'Reservations';
    }

    /**
     * Route Name Prefix
     */
    protected function routeName(): string
    {
        return 'user.reservations';
    }

    /**
     * Service Instance
     */
    protected function service(): ResourceService
    {
        return new ReservationService;
    }

    /**
     * Index - List Reservations
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return $this->defaultIndex($request, $user->reservations(), IndexResource::class);
    }

    /**
     * Create - Show Reservation Form
     */
    public function create(Request $request): Response
    {
        return $this->defaultCreate($request, [
            'paymentMethod' => PaymentMethod::renderSelect(),
        ]);
    }

    /**
     * Store - Handle New Reservation
     */
    public function store(ReservationRequest $request): RedirectResponse
    {
        return $this->defaultStore($request, new Reservation);
    }

    /**
     * Show - View Reservation Details
     */
    public function show(Request $request, Reservation $reservation): Response
    {
        return $this->defaultShow($request, $reservation, ShowResource::class);
    }

    /**
     * Archive - Soft Delete a Reservation
     */
    public function archive(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->service()->setModel($reservation)->delete($request);

        return to_route($this->routeName().'.index')
            ->with('success', 'Reservation has been archived.');
    }

    /**
     * Restore - Recover an Archived Reservation
     */
    public function restore(Request $request, Reservation $reservation): RedirectResponse
    {
        return $this->defaultRestore($request, $reservation);
    }
}
