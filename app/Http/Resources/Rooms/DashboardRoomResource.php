<?php

namespace App\Http\Resources\Rooms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->files()->where('collection', 'gallery')->first();

        return [
            'id' => $this->id,
            'roomNumber' => $this->room_number,
            'type' => $this->type->pill(),
            'capacity' => $this->capacity,
            'price' => (float) $this->price,
            'isActive' => $this->is_active,
            'image' => $image?->url,
            'hotelName' => $this->hotel->name,
        ];
    }
}
