<?php

namespace App\Http\Controllers;

use App\Http\Resources\Hotels\DashboardHotelResource;
use App\Http\Resources\Rooms\DashboardRoomResource;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Render the Dashboard
     *
     * Caches dashboard data for 10 minutes to reduce database load
     * on repeated visits to the dashboard page.
     */
    public function __invoke(): Response
    {
        $data = Cache::remember('dashboard_data', now()->addMinutes(10), function () {
            $hotels = Hotel::withCount('rooms')->with('files')->get();
            $rooms = Room::with(['hotel', 'files'])->get();

            return [
                'hotelsCount' => Hotel::count(),
                'roomsCount' => Room::count(),
                'reservationsCount' => Reservation::count(),
                'hotels' => DashboardHotelResource::collection($hotels)->resolve(),
                'rooms' => DashboardRoomResource::collection($rooms)->resolve(),
            ];
        });

        return Inertia::render('Dashboard', $data);
    }
}
