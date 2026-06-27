<?php

namespace Database\Factories;

use App\Enums\RoomType;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'type' => $this->faker->randomElement(RoomType::cases()),
            'capacity' => $this->faker->numberBetween(1, 4),
            'price' => $this->faker->numberBetween(1000, 5000),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Room $room) {
            $colors = ['4F46E5', '059669', 'DC2626', '2563EB', '7C3AED', 'DB2777', '0891B2', '65A30D'];
            $color = $this->faker->randomElement($colors);

            $typeLabels = [
                RoomType::SINGLE->value => 'SINGLE',
                RoomType::DOUBLE->value => 'DOUBLE',
                RoomType::SUITE->value => 'SUITE',
                RoomType::DELUXE->value => 'DELUXE',
            ];
            $label = $typeLabels[$room->type->value] ?? 'ROOM';

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
  <rect width="400" height="300" fill="#{$color}22"/>
  <rect x="50" y="80" width="300" height="180" rx="6" fill="#{$color}33"/>
  <rect x="80" y="110" width="100" height="80" rx="4" fill="#{$color}"/>
  <rect x="200" y="110" width="100" height="80" rx="4" fill="#{$color}"/>
  <text x="200" y="55" font-family="Arial" font-size="22" fill="#{$color}" text-anchor="middle" font-weight="bold">{$label} - {$room->room_number}</text>
</svg>
SVG;

            $filename = Str::slug("room-{$room->room_number}").'.svg';
            $path = "rooms/{$room->id}/{$filename}";

            Storage::disk('public')->put($path, $svg);

            $room->files()->create([
                'collection' => 'gallery',
                'disk' => 'public',
                'path' => $path,
                'original_name' => $filename,
                'mime_type' => 'image/svg+xml',
                'size' => strlen($svg),
            ]);
        });
    }
}
