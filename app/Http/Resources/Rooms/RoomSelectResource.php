<?php

namespace App\Http\Resources\Rooms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomSelectResource extends JsonResource
{
    /**
     * Transform Room Select Resource
     */
    public function toArray(Request $request): array
    {
        $hero = $this->files()->where('collection', 'gallery')->first();

        return [
            'id' => $this->getKey(),
            'roomNumber' => $this->room_number,
            'type' => $this->type->pill(),
            'capacity' => $this->capacity,
            'price' => $this->price,
            'isActive' => $this->is_active,
            'image' => $hero?->url,
            'hotel' => $this->hotel->only(['id', 'name']),
        ];
    }
}
