<?php

namespace App\Http\Controllers;

use App\Http\Resources\Hotels\DashboardHotelResource;
use App\Http\Resources\Rooms\DashboardRoomResource;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Render the Dashboard
     */
    public function __invoke(): Response
    {
        $hotels = Hotel::withCount('rooms')->with('files')->get();
        $rooms = Room::with(['hotel', 'files'])->get();

        return Inertia::render('Dashboard', [
            'hotelsCount' => Hotel::count(),
            'roomsCount' => Room::count(),
            'reservationsCount' => Reservation::count(),
            'hotels' => DashboardHotelResource::collection($hotels)->resolve(),
            'rooms' => DashboardRoomResource::collection($rooms)->resolve(),
        ]);
    }
}
