<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+30 days'));
        $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 7));

        return [
            'user_id' => null,
            'room_id' => null,
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'guest' => $this->faker->numberBetween(1, 4),
            'amount' => $this->faker->numberBetween(1000, 30000),
            'status' => ReservationStatus::CONFIRMED,
        ];
    }

    /** A reservation that already happened and finished. */
    public function past(): static
    {
        return $this->state(function () {
            $checkIn = Carbon::instance($this->faker->dateTimeBetween('-60 days', '-10 days'));
            $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 7));

            return [
                'check_in_at' => $checkIn,
                'check_out_at' => $checkOut,
                'status' => ReservationStatus::COMPLETED,
            ];
        });
    }

    /** A reservation currently in progress (check-in passed, check-out hasn't). */
    public function current(): static
    {
        return $this->state(function () {
            $checkIn = Carbon::instance($this->faker->dateTimeBetween('-3 days', '-1 days'));
            $checkOut = Carbon::now()->addDays($this->faker->numberBetween(1, 5));

            return [
                'check_in_at' => $checkIn,
                'check_out_at' => $checkOut,
                'status' => ReservationStatus::CONFIRMED,
            ];
        });
    }

    /** A reservation booked for the future. */
    public function future(): static
    {
        return $this->state(function () {
            $checkIn = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+30 days'));
            $checkOut = $checkIn->copy()->addDays($this->faker->numberBetween(1, 7));

            return [
                'check_in_at' => $checkIn,
                'check_out_at' => $checkOut,
                'status' => $this->faker->randomElement([ReservationStatus::PENDING, ReservationStatus::COMPLETED]),
            ];
        });
    }

    /** A cancelled reservation (soft-deleted), can apply to any timeframe. */
    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => ReservationStatus::CANCELLED]);
    }
}
