<?php

namespace App\Models;

use App\Casts\AsDecimal;
use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'room_id', 'status', 'amount', 'check_in_at', 'check_out_at', 'guest'])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'amount' => AsDecimal::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */
    /**
     * Duration of the stay, in nights.
     */
    public function duration(): int
    {
        return $this->check_in_at->diffInDays($this->check_out_at);
    }

    /**
     * Total cost of the reservation, based on room rate x duration.
     */
    public function calculateTotalCost(): float
    {
        return $this->duration() * $this->room->price;
    }
}
