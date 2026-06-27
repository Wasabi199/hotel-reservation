<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\Concerns\HasCreateProcessor;
use App\Services\Concerns\HasDeleteProcessor;
use App\Services\Concerns\HasListProcessor;
use App\Services\Concerns\HasReadProcessor;
use App\Services\Concerns\HasRestoreProcessor;
use App\Services\Concerns\ResourceService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class ReservationService extends ResourceService
{
    use HasCreateProcessor;
    use HasDeleteProcessor;
    use HasListProcessor;
    use HasReadProcessor;
    use HasRestoreProcessor;

    /**
     * Index - List Reservations
     */
    public function index(Request $request, array $additionalProps = []): Response
    {
        $this->with(['room', 'user']);

        $trashed = $request->input('trashed');

        if ($trashed === 'only') {
            $this->where(fn ($query) => $query->onlyTrashed());
        } elseif ($trashed === 'with') {
            $this->where(fn ($query) => $query->withTrashed());
        }

        $this->defaultOrderBy('created_at', 'desc');

        return $this->defaultIndex($request, $additionalProps);
    }

    /**
     * Prepare Store Data
     */
    public function prepareStoreData(FormRequest $request): array
    {
        $checkIn = Carbon::parse($request->check_in_at);
        $checkOut = Carbon::parse($request->check_out_at);
        $nights = $checkIn->diffInDays($checkOut);
        $room = Room::findOrFail($request->validated('room_id'));
        $amount = $nights * $room->price;

        return array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'status' => ReservationStatus::PENDING,
            'amount' => $amount,
        ]);
    }

    /**
     * Store - Create Reservation
     */
    public function store(FormRequest $request): Model
    {
        return DB::transaction(function () use ($request) {
            $reservation = $this->defaultStore($request);

            $reservation->payment()->create([
                'amount' => $reservation->amount,
                'status' => PaymentStatus::PENDING,
                'payment_method' => $request->validated('payment_method', PaymentMethod::CASH()),
            ]);

            return $reservation;
        });
    }

    /**
     * Show - View a Reservation
     */
    public function show(Request $request, array $additionalProps = []): Response
    {
        $this->model->with(['room', 'user']);

        return $this->defaultShow($request, $additionalProps);
    }

    /**
     * Delete - Archive a Reservation
     */
    public function delete(Request $request, bool $forceDelete = false): Model
    {
        return $this->defaultDelete($request, $forceDelete);
    }

    /**
     * Restore - Recover an Archived Reservation
     */
    public function restore(Request $request): Model
    {
        return $this->defaultRestore($request);
    }
}
