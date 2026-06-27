<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $rooms = Room::all();

        if ($users->isEmpty() || $rooms->isEmpty()) {
            $this->command->warn('Seed users and rooms before reservations.');

            return;
        }

        // // 5 past, 5 current, 5 future = 15 total, per spec
        $this->createBatch('past', 5, $users, $rooms);
        $this->createBatch('current', 5, $users, $rooms);
        $this->createBatch('future', 5, $users, $rooms);
    }

    /**
     * Handle Create Batch Reservation
     *
     * @param  User  $users
     * @param  Room  $rooms
     */
    private function createBatch(string $state, int $count, $users, $rooms): void
    {
        for ($i = 0; $i < $count; $i++) {
            $room = $rooms->random();
            $user = $users->random();

            $reservation = Reservation::factory()
                ->{$state}()
                ->make([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                ]);

            // Calculate total_price now that we know the room's rate
            $nights = $reservation->check_in_at->diffInDays($reservation->check_out_at);
            $reservation->amount = $nights * $room->price;

            $reservation->save();
        }
    }
}
