<?php

namespace App\Http\Resources\Hotels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableRoomResource extends JsonResource
{
    /**
     * Transform Available Room Resource
     */
    public function toArray(Request $request): array
    {
        $hero = $this->files()->where('collection', 'gallery')->first();

        return [
            'id' => $this->getKey(),
            'roomNumber' => $this->room_number,
            'type' => $this->type->pill(),
            'capacity' => $this->capacity,
            'price' => (float) $this->price,
            'isActive' => $this->is_active,
            'image' => $hero?->url,
            'hotel' => [
                'id' => $this->hotel->id,
                'name' => $this->hotel->name,
            ],
        ];
    }
}
