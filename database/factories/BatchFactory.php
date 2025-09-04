<?php

namespace Database\Factories;

use App\Enums\BatchStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status'             => $this->faker->randomElement(BatchStatusEnum::cases()),
            'total_files'        => $this->faker->numberBetween(1, 10),
            'processed_files'    => fn(array $attributes) => $this->faker->numberBetween(0, $attributes['total_files']),
            'failed_files'       => fn(array $attributes) => $this->faker->numberBetween(0, $attributes['total_files'] - $attributes['processed_files']),
            'error_message'      => fn(array $attributes) => $attributes['status'] === BatchStatusEnum::FAILED
                ? $this->faker->sentence()
                : null,
            'processing_options' => [
                'operation' => $this->faker->randomElement(['crop', 'resize', 'normalize']),
                'width'     => $this->faker->optional(2 / 3)->numberBetween(100, 1920),
                'height'    => $this->faker->optional(2 / 3)->numberBetween(100, 1080),
                'quality'   => $this->faker->numberBetween(60, 100),
            ],
            'user_id'            => User::factory(),
            'processed_at'       => fn(array $attributes) => $attributes['status'] === BatchStatusEnum::COMPLETED
                ? $this->faker->dateTimeThisMonth()
                : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'          => BatchStatusEnum::PENDING,
            'processed_files' => 0,
            'failed_files'    => 0,
            'processed_at'    => null,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'       => BatchStatusEnum::PROCESSING,
            'processed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'          => BatchStatusEnum::COMPLETED,
            'processed_files' => $attributes['total_files'],
            'failed_files'    => 0,
            'processed_at'    => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'          => BatchStatusEnum::FAILED,
            'processed_files' => 0,
            'failed_files'    => $attributes['total_files'],
            'processed_at'    => now(),
        ]);
    }

    public function partial(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'       => BatchStatusEnum::PARTIAL,
            'processed_at' => now(),
        ]);
    }
}
