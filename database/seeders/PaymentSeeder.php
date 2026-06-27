<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = Reservation::all();

        if ($reservations->isEmpty()) {
            $this->command->warn('Seed reservations before payments.');

            return;
        }

        foreach ($reservations as $reservation) {
            $state = $this->mapReservationStatusToPaymentStatus($reservation->status);

            Payment::factory()
                ->create([
                    'reservation_id' => $reservation->id,
                    'amount' => $reservation->amount,
                ]);
        }
    }

    private function mapReservationStatusToPaymentStatus(ReservationStatus $reservationStatus): PaymentStatus
    {
        return match ($reservationStatus) {
            ReservationStatus::COMPLETED => PaymentStatus::COMPLETED,
            ReservationStatus::CONFIRMED => PaymentStatus::COMPLETED,
            ReservationStatus::PENDING => PaymentStatus::PENDING,
            ReservationStatus::CANCELLED => fake()->boolean(50) ? PaymentStatus::REFUNDED : PaymentStatus::FAILED,
        };
    }
}
