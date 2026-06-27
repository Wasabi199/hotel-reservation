<?php

namespace App\Http\Resources\Hotels;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardHotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hero = $this->files()->where('collection', 'hero')->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'roomsCount' => $this->rooms_count ?? $this->rooms()->count(),
            'image' => $hero?->url,
        ];
    }
}
