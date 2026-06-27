<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupUnpaidReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel unpaid reservations that are older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = Carbon::now()->subHours(24);

        $count = Reservation::where('status', ReservationStatus::PENDING)
            ->where('created_at', '<', $cutoff)
            ->whereDoesntHave('payment', function ($query) {
                $query->where('status', PaymentStatus::COMPLETED);
            })
            ->update(['status' => ReservationStatus::CANCELLED]);

        $this->info("Cancelled {$count} unpaid reservation(s) older than 24 hours.");

        return Command::SUCCESS;
    }
}
