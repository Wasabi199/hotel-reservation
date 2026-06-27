<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Reservation;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_uuid')->nullable()->unique();
            $table->foreignIdFor(Reservation::class)->index()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->unsignedTinyInteger('status')->index()->comment(PaymentStatus::class);
            $table->string('payment_method')->index()->comment(PaymentMethod::class);
            $table->timestamp('paid_at')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
