<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiKey = 'hotel-api-secret';

    protected function apiHeaders(): array
    {
        return ['X-API-Key' => $this->apiKey];
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/hotels/{hotel}/available-rooms
    |--------------------------------------------------------------------------
    */

    public function test_available_rooms_requires_api_key(): void
    {
        $hotel = Hotel::factory()->create();

        $this->getJson("/api/hotels/{$hotel->id}/available-rooms?check_in_at=2026-07-01&check_out_at=2026-07-03")
            ->assertStatus(401)
            ->assertJson(['message' => 'Invalid or missing API key.']);
    }

    public function test_available_rooms_returns_available_rooms(): void
    {
        $hotel = Hotel::factory()->create();
        Room::factory()->count(3)->create(['hotel_id' => $hotel->id]);

        $this->getJson("/api/hotels/{$hotel->id}/available-rooms?check_in_at=2026-07-01&check_out_at=2026-07-03", $this->apiHeaders())
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'roomNumber', 'type', 'capacity', 'price', 'isActive', 'image', 'hotel'],
                ],
            ]);
    }

    public function test_available_rooms_validates_dates(): void
    {
        $hotel = Hotel::factory()->create();

        $this->getJson("/api/hotels/{$hotel->id}/available-rooms?check_in_at=invalid&check_out_at=2026-07-03", $this->apiHeaders())
            ->assertStatus(422);

        $this->getJson("/api/hotels/{$hotel->id}/available-rooms?check_in_at=2026-07-05&check_out_at=2026-07-03", $this->apiHeaders())
            ->assertStatus(422);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/reservations
    |--------------------------------------------------------------------------
    */

    public function test_store_requires_api_key(): void
    {
        $this->postJson('/api/reservations', [])
            ->assertStatus(401);
    }

    public function test_store_creates_reservation(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'capacity' => 4,
            'price' => 1000,
        ]);

        $payload = [
            'room_id' => $room->id,
            'check_in_at' => '2026-07-01',
            'check_out_at' => '2026-07-04',
            'guest' => 2,
            'payment_method' => 'cash',
        ];

        $this->actingAs($user)
            ->postJson('/api/reservations', $payload, $this->apiHeaders())
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'room', 'user', 'checkInAt', 'checkOutAt',
                    'status', 'guest', 'amount', 'duration', 'payment', 'createdAt',
                ],
            ]);

        $this->assertDatabaseHas('reservations', [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'guest' => 2,
            'status' => ReservationStatus::PENDING->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'status' => PaymentStatus::PENDING->value,
            'payment_method' => PaymentMethod::CASH->value,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->postJson('/api/reservations', [], $this->apiHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'room_id', 'check_in_at', 'check_out_at', 'guest', 'payment_method',
            ]);
    }

    public function test_store_checks_room_capacity(): void
    {
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'capacity' => 2,
        ]);

        $payload = [
            'room_id' => $room->id,
            'check_in_at' => '2026-07-01',
            'check_out_at' => '2026-07-04',
            'guest' => 5,
            'payment_method' => 'cash',
        ];

        $this->postJson('/api/reservations', $payload, $this->apiHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['guest']);
    }

    public function test_store_rejects_overlapping_reservation(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'capacity' => 4,
        ]);

        Reservation::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'check_in_at' => '2026-07-01',
            'check_out_at' => '2026-07-05',
            'status' => ReservationStatus::CONFIRMED,
        ]);

        $payload = [
            'room_id' => $room->id,
            'check_in_at' => '2026-07-02',
            'check_out_at' => '2026-07-04',
            'guest' => 1,
            'payment_method' => 'cash',
        ];

        $this->actingAs($user)
            ->postJson('/api/reservations', $payload, $this->apiHeaders())
            ->assertStatus(422);
    }

    public function test_store_check_in_must_be_today_or_future(): void
    {
        $payload = [
            'room_id' => 1,
            'check_in_at' => Carbon::yesterday()->format('Y-m-d'),
            'check_out_at' => '2026-07-04',
            'guest' => 1,
            'payment_method' => 'cash',
        ];

        $this->postJson('/api/reservations', $payload, $this->apiHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['check_in_at']);
    }

    public function test_store_check_out_must_be_after_check_in(): void
    {
        $payload = [
            'room_id' => 1,
            'check_in_at' => '2026-07-04',
            'check_out_at' => '2026-07-01',
            'guest' => 1,
            'payment_method' => 'cash',
        ];

        $this->postJson('/api/reservations', $payload, $this->apiHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['check_out_at']);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/reservations/{reservation}
    |--------------------------------------------------------------------------
    */

    public function test_show_returns_404_for_missing_reservation(): void
    {
        $this->getJson('/api/reservations/99999', $this->apiHeaders())
            ->assertStatus(404);
    }

    public function test_show_requires_api_key(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create(['hotel_id' => $hotel->id]);
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $this->getJson("/api/reservations/{$reservation->id}")
            ->assertStatus(401);
    }

    public function test_show_returns_reservation(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create(['hotel_id' => $hotel->id]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);

        $this->getJson("/api/reservations/{$reservation->id}", $this->apiHeaders())
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id', 'room', 'user', 'checkInAt', 'checkOutAt',
                    'status', 'guest', 'amount', 'duration', 'payment', 'createdAt',
                ],
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | php artisan reservations:cleanup
    |--------------------------------------------------------------------------
    */

    public function test_cleanup_cancels_old_unpaid_reservations(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create(['hotel_id' => $hotel->id]);

        $oldPending = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => Carbon::now()->addDay(),
            'check_out_at' => Carbon::now()->addDays(3),
            'created_at' => Carbon::now()->subHours(30),
            'status' => ReservationStatus::PENDING,
        ]);

        $recentPending = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => Carbon::now()->addDay(),
            'check_out_at' => Carbon::now()->addDays(3),
            'created_at' => Carbon::now()->subHours(2),
            'status' => ReservationStatus::PENDING,
        ]);

        $this->artisan('reservations:cleanup')
            ->assertSuccessful()
            ->expectsOutputToContain('Cancelled 1 unpaid reservation(s)');

        $this->assertDatabaseHas('reservations', [
            'id' => $oldPending->id,
            'status' => ReservationStatus::CANCELLED->value,
        ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $recentPending->id,
            'status' => ReservationStatus::PENDING->value,
        ]);
    }

    public function test_cleanup_does_not_cancel_confirmed_reservations(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create(['hotel_id' => $hotel->id]);

        Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => Carbon::now()->addDay(),
            'check_out_at' => Carbon::now()->addDays(3),
            'created_at' => Carbon::now()->subHours(30),
            'status' => ReservationStatus::CONFIRMED,
        ]);

        $this->artisan('reservations:cleanup')
            ->assertSuccessful()
            ->expectsOutputToContain('Cancelled 0');
    }

    public function test_cleanup_output_when_nothing_to_cancel(): void
    {
        $this->artisan('reservations:cleanup')
            ->assertSuccessful()
            ->expectsOutputToContain('Cancelled 0');
    }
}
