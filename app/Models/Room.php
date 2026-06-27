<?php

namespace App\Models;

use App\Casts\AsDecimal;
use App\Enums\ReservationStatus;
use App\Enums\RoomType;
use Carbon\Carbon;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['hotel_id', 'room_number', 'type', 'capacity', 'price', 'description', 'is_active'])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => RoomType::class,
            'price' => AsDecimal::class,
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeAvailable(Builder $query, string $checkInAt, string $checkOutAt): Builder
    {
        $checkInAt = Carbon::parse($checkInAt);
        $checkOutAt = Carbon::parse($checkOutAt);

        return $query->where('is_active', true)
            ->whereDoesntHave('reservation', function ($q) use ($checkInAt, $checkOutAt) {
                $q->whereNotIn('status', [ReservationStatus::CANCELLED])
                    ->where('check_in_at', '<', $checkInAt)
                    ->where('check_out_at', '>', $checkOutAt);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function reservation(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(UploadedFile::class, 'fileable');
    }
}
