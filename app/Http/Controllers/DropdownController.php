<?php

namespace App\Http\Controllers;

use App\Http\Resources\Rooms\RoomSelectResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    /**
     * Available Rooms (Dropdown)
     */
    public function availableRooms(Request $request): JsonResponse
    {
        $request->validate([
            'check_in_at' => ['required', 'date'],
            'check_out_at' => ['required', 'date', 'after:check_in_at'],
        ]);

        $query = Room::available($request->check_in_at, $request->check_out_at)
            ->with(['hotel', 'files']);

        if ($request->filled('guest')) {
            $query->where('capacity', '>=', (int) $request->guest);
        }

        $rooms = $query->get();

        return response()->json([
            'data' => RoomSelectResource::collection($rooms),
        ]);
    }
}
