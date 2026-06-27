<?php

namespace App\Http\Resources\Reservations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform Reservation Show Resource
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => '#'.$this->room?->room_number,
            'user' => $this->user
                ? $this->user->only(['id', 'name', 'email'])
                : null,
            'status' => $this->status->pill(),
            'amount' => $this->calculateTotalCost(),
            'checkInAt' => $this->check_in_at,
            'checkOutAt' => $this->check_out_at,
            'duration' => $this->duration(),
            'createdAt' => $this->created_at,
            'deletedAt' => $this->deleted_at,
        ];
    }
}
