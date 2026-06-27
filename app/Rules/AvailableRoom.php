<?php

namespace App\Rules;

use App\Models\Room;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableRoom implements ValidationRule
{
    /**
     * Constructor
     */
    public function __construct(
        protected mixed $checkInAt,
        protected mixed $checkOutAt,
    ) {}

    /**
     * Validate Room Availability
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Room::where('id', $value)
            ->available($this->checkInAt, $this->checkOutAt)
            ->exists();

        if (! $exists) {
            $fail('The selected room is not available for the given dates.');
        }
    }
}
