<?php

use App\Enums\ReservationStatus;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Room::class)->index()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('status')->index()->default(ReservationStatus::PENDING)->comment(ReservationStatus::class);
            $table->unsignedBigInteger('amount');
            $table->timestamp('check_in_at')->index();
            $table->timestamp('check_out_at')->index();
            $table->unsignedInteger('guest');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
