<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => null,
            'amount' => 0,
            'status' => PaymentStatus::PENDING,
            'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
            'transaction_uuid' => $this->faker->unique()->uuid(),
            'paid_at' => null,
        ];
    }

    /** Payment fully completed. */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::COMPLETED,
            'paid_at' => now(),
        ]);
    }

    /** Payment still pending (not yet paid). */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::PENDING,
            'paid_at' => null,
        ]);
    }

    /** Payment attempt failed. */
    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::FAILED,
            'paid_at' => null,
        ]);
    }

    /** Payment was completed, then refunded. */
    public function refunded(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::REFUNDED,
            'paid_at' => now()->subDays($this->faker->numberBetween(1, 10)),
        ]);
    }
}
