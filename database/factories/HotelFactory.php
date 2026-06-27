<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Hotel',
            'address' => $this->faker->streetAddress(),
            'description' => $this->faker->paragraph(),
            'contact_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Hotel $hotel) {
            $colors = ['4F46E5', '059669', 'DC2626', '2563EB', '7C3AED', 'DB2777'];
            $color = $this->faker->randomElement($colors);

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <rect width="800" height="600" fill="#{$color}22"/>
  <rect x="100" y="200" width="600" height="350" rx="8" fill="#{$color}44"/>
  <rect x="150" y="250" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="290" y="250" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="430" y="250" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="150" y="360" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="290" y="360" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="430" y="360" width="120" height="90" rx="4" fill="#{$color}"/>
  <rect x="300" y="80" width="200" height="100" rx="4" fill="#{$color}bb"/>
  <text x="400" y="135" font-family="Arial" font-size="28" fill="white" text-anchor="middle" font-weight="bold">HOTEL</text>
</svg>
SVG;

            $filename = Str::slug($hotel->name).'.svg';
            $path = "hotels/{$hotel->id}/{$filename}";

            Storage::disk('public')->put($path, $svg);

            $hotel->files()->create([
                'collection' => 'hero',
                'disk' => 'public',
                'path' => $path,
                'original_name' => $filename,
                'mime_type' => 'image/svg+xml',
                'size' => strlen($svg),
            ]);
        });
    }
}
