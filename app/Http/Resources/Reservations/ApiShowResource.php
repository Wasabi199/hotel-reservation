<?php

namespace App\Http\Resources\Reservations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiShowResource extends JsonResource
{
    /**
     * Transform API Reservation Resource
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room' => $this->room ? [
                'id' => $this->room->id,
                'roomNumber' => $this->room->room_number,
                'type' => $this->room->type->pill(),
                'price' => (float) $this->room->price,
            ] : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'checkInAt' => $this->check_in_at,
            'checkOutAt' => $this->check_out_at,
            'status' => $this->status->pill(),
            'guest' => $this->guest,
            'amount' => (float) $this->amount,
            'duration' => $this->duration(),
            'payment' => $this->payment ? [
                'status' => $this->payment->status->pill(),
                'method' => $this->payment->payment_method->pill(),
                'amount' => (float) $this->payment->amount,
            ] : null,
            'createdAt' => $this->created_at,
        ];
    }
}
