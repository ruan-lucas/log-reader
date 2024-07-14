<?php

namespace Database\Factories;

use App\Models\Consumer;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'consumer_id' => Consumer::factory(),
            'service_id' => Service::factory(),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'uri' => $this->faker->url,
            'status' => $this->faker->numberBetween(200, 500),
            'request_time' => $this->faker->numberBetween(50, 200),
            'proxy_time' => $this->faker->numberBetween(25, 100),
            'gateway_time' => $this->faker->numberBetween(25, 100),
        ];
    }
}
