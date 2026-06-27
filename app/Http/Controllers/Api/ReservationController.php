<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReservationRequest;
use App\Http\Resources\Hotels\AvailableRoomResource;
use App\Http\Resources\Reservations\ApiShowResource;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $service,
    ) {}

    /**
     * Get Available Rooms for a Hotel
     *
     * Caches available rooms per hotel and date range for 5 minutes
     * so rapid API calls for the same dates hit the file cache instead
     * of querying the database each time.
     */
    public function availableRooms(Request $request, Hotel $hotel): JsonResponse
    {
        $request->validate([
            'check_in_at' => ['required', 'date', 'after_or_equal:today'],
            'check_out_at' => ['required', 'date', 'after:check_in_at'],
        ]);

        $cacheKey = 'api_available_rooms_'.$hotel->id.'_'.md5(
            $request->check_in_at.'|'.$request->check_out_at
        );

        $rooms = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $hotel) {
            return $hotel->availableRooms($request->check_in_at, $request->check_out_at)
                ->with('files')
                ->get();
        });

        return response()->json([
            'data' => AvailableRoomResource::collection($rooms),
        ]);
    }

    /**
     * Create a New Reservation
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $reservation = $this->service->setModel(new Reservation)->store($request);

        return response()->json([
            'message' => 'Reservation created successfully.',
            'data' => new ApiShowResource($reservation->load(['room', 'user', 'payment'])),
        ], 201);
    }

    /**
     * Show a Specific Reservation
     */
    public function show(Request $request, Reservation $reservation): JsonResponse
    {
        $reservation->load(['room', 'user', 'payment']);

        return response()->json([
            'data' => new ApiShowResource($reservation),
        ]);
    }
}
