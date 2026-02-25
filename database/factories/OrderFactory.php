<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = \App\Models\Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departureDays = (int) rand(1, 30);
        $returnDays = (int) rand(31, 60);
        
        $departureDate = \Carbon\Carbon::now()->addDays($departureDays);
        $returnDate = \Carbon\Carbon::now()->addDays($returnDays);

        return [
            'user_id' => User::factory(),
            'customer_name' => $this->faker->name(),
            'destination' => $this->faker->randomElement([
                'São Paulo',
                'Rio de Janeiro',
                'Belo Horizonte',
                'Salvador',
                'Brasília',
                'Fortaleza',
                'Recife',
                'Porto Alegre',
                'Curitiba',
                'Manaus',
            ]),
            'departure_date' => $departureDate->format('Y-m-d'),
            'return_date' => $returnDate->format('Y-m-d'),
            'status' => $this->faker->randomElement([
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_CANCELLED,
            ]),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_CONFIRMED,
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_CANCELLED,
        ]);
    }
}
