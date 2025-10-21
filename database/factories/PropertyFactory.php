<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $type = $this->faker->randomElement(['house', 'apartment', 'condo', 'townhouse', 'villa', 'land', 'commercial']);
        $listingType = $this->faker->randomElement(['sale', 'rent']);
        $city = $this->faker->randomElement(['Bahir Dar', 'Gondar', 'Hawassa', 'Dessie', 'Adama', 'Arba Minch', 'Addis Abeba']);

        // Pricing based on type and listing
        $basePrice = match ($type) {
            'land' => $this->faker->numberBetween(5000000, 50000000),
            'apartment' => $this->faker->numberBetween(15000000, 80000000),
            'house' => $this->faker->numberBetween(25000000, 150000000), // TZS
            'villa' => $this->faker->numberBetween(100000000, 500000000),
            'commercial' => $this->faker->numberBetween(50000000, 300000000),
            default => $this->faker->numberBetween(20000000, 100000000),
        };

        //rent prices are typically monthly
        $price = $listingType === 'rent' ? $basePrice / 100 : $basePrice;
        $title = $this->generatePropertyTitle($type, $city);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->generateDescription($type),
            'type' => $type,
            'listing_type' => $listingType,
            'status' => $this->faker->randomElement(['draft', 'available', 'sold', 'rented', 'pending']), // More available
            'price' => $price,
            'address' => $this->faker->streetAddress(),
            'city' => $city,
            'state' => $this->faker->randomElement(['Bahir Dar', 'Gondar', 'Dessie', 'Hawassa', 'Adama']),
            'country' => 'Tanzania',
            'postal_code' => $this->faker->optional()->postcode(),
            'latitude' => $this->getLatitudeForCity($city),
            'longitude' => $this->getLongitudeForCity($city),
            'bedrooms' => $type === 'land' ? null : $this->faker->numberBetween(1, 6),
            'bathrooms' => $type === 'land' ? null : $this->faker->numberBetween(1, 4),
            'total_area' => $this->faker->numberBetween(500, 5000),
            'built_year' => $type === 'land' ? null : $this->faker->numberBetween(1990, 2025),
            'furnished' => $this->faker->boolean(30), // 30% chance
            'parking' => $this->faker->boolean(70), // 70% chance
            'parking_spaces' => $this->faker->boolean(70) ? $this->faker->numberBetween(1, 3) : null,
            'features' => $this->generateFeatures($type),
            'images' => [], // We'll populate this separately or manually
            'meta_title' => $title . ' - Best Real Estate in ' . $city,
            'meta_description' => Str::limit($this->generateDescription($type), 155),
            'is_featured' => $this->faker->boolean(20), // 20% featured
            'is_active' => $this->faker->boolean(90), // 90% active
            'featured_until' => $this->faker->boolean(20) ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            'contact_name' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
        ];

    }
    private function generatePropertyTitle(string $type, string $city): string
    {
        $adjectives = ['Modern', 'Luxury', 'Spacious', 'Beautiful', 'Stunning', 'Executive', 'Premium', 'Elegant'];
        $adjective = $this->faker->randomElement($adjectives);

        return match ($type) {
            'house' => "{$adjective} Family House in {$city}",
            'apartment' => "{$adjective} Apartment in {$city}",
            'villa' => "{$adjective} Villa in {$city}",
            'condo' => "{$adjective} Condominium in {$city}",
            'townhouse' => "{$adjective} Townhouse in {$city}",
            'land' => "Prime Land for Sale in {$city}",
            'commercial' => "{$adjective} Commercial Property in {$city}",
            default => "{$adjective} Property in {$city}",
        };
    }

    private function generateDescription(string $type): string
    {
        $descriptions = [
            'house' => 'This beautiful family home offers comfortable living with modern amenities. Perfect for families looking for a peaceful neighborhood with easy access to schools and shopping centers.',
            'apartment' => 'Modern apartment featuring contemporary design and premium finishes. Located in a secure building with excellent facilities and convenient transportation links.',
            'villa' => 'Luxurious villa showcasing exceptional architecture and high-end amenities. Set in an exclusive location offering privacy and tranquility while being close to city conveniences.',
            'condo' => 'Well-appointed condominium in a prestigious development. Features modern facilities and excellent security in a prime location.',
            'townhouse' => 'Stylish townhouse offering the perfect blend of privacy and community living. Modern design with quality finishes throughout.',
            'land' => 'Prime development land in an excellent location. Perfect for residential or commercial development with great potential for appreciation.',
            'commercial' => 'Excellent commercial property in a high-traffic area. Ideal for businesses looking for visibility and accessibility.',
        ];

        return $descriptions[$type] ?? 'Exceptional property offering great value and potential.';
    }

    private function getLatitudeForCity(string $city): float
    {
        return match ($city) {
            'Bahir Dar' => $this->faker->randomFloat(6, -6.9, -6.7),
            'Gondar' => $this->faker->randomFloat(6, -3.4, -3.3),
            'Dessie' => $this->faker->randomFloat(6, -2.6, -2.4),
            'Hawassa' => $this->faker->randomFloat(6, -6.3, -6.1),
            'Adama' => $this->faker->randomFloat(6, -8.9, -8.8),
            'Arba Minch' => $this->faker->randomFloat(6, -5.1, -5.0),
            'Addis Abeba' => $this->faker->randomFloat(6, -6.9, -6.7),
            default => $this->faker->randomFloat(6, -10, -1),
        };
    }
    private function getLongitudeForCity(string $city): float
    {
        return match ($city) {
            'Bahir Dar' => $this->faker->randomFloat(6, 39.1, 39.3),
            'Gondar' => $this->faker->randomFloat(6, 36.7, 36.9),
            'Dessie' => $this->faker->randomFloat(6, 32.8, 33.0),
            'Hawassa' => $this->faker->randomFloat(6, 35.7, 35.9),
            'Adama' => $this->faker->randomFloat(6, 33.4, 33.6),
            'Tanga' => $this->faker->randomFloat(6, 39.0, 39.2),
            'Addis Abeba' => $this->faker->randomFloat(6, 37.5, 37.7),
            default => $this->faker->randomFloat(6, 30, 40),
        };
    }

    private function generateFeatures(string $type): array
    {
        $commonFeatures = ['Security', 'Parking', 'Water', 'Electricity'];

        $specificFeatures = match ($type) {
            'house', 'villa' => ['Garden', 'Garage', 'Swimming Pool', 'Balcony', 'Fireplace', 'Study Room'],
            'apartment', 'condo' => ['Elevator', 'Gym', 'Swimming Pool', 'Balcony', 'Concierge', 'Rooftop'],
            'townhouse' => ['Patio', 'Garage', 'Garden', 'Balcony', 'Storage'],
            'commercial' => ['Reception Area', 'Conference Room', 'Parking', 'Air Conditioning', 'Generator'],
            'land' => ['Fenced', 'Road Access', 'Title Deed', 'Survey Plan'],
            default => ['Modern Fixtures', 'Tiled Floors'],
        };

        $allFeatures = array_merge($commonFeatures, $specificFeatures);
        $selectedFeatures = $this->faker->randomElements($allFeatures, $this->faker->numberBetween(3, 7));

        return array_unique($selectedFeatures);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => $this->faker->dateTimeBetween('now', '+6 months'),
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
        ]);
    }

    public function forRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_type' => 'rent',
            'price' => $attributes['price'] / 100, // Monthly rent
        ]);
    }
}