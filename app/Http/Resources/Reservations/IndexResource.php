<?php

namespace App\Http\Resources\Reservations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform Reservation Index Resource
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => '#'.$this->room?->room_number,
            'user' => $this->user
                ? $this->user->only(['id', 'name', 'email'])
                : null,
            'status' => $this->status->pill(),
            'amount' => $this->calculateTotalCost(),
            'duration' => $this->duration(),
            'createdAt' => $this->created_at,
            'deletedAt' => $this->deleted_at,
        ];
    }
}
