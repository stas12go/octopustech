<?php

namespace Database\Factories;

use App\Enums\FileStatusEnum;
use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = $this->faker->randomElement(['jpeg', 'jpg', 'bmp', 'png']);

        return [
            'batch_id'           => Batch::factory(),
            'original_name'      => "{$this->faker->word}.{$extension}",
            'original_path'      => "uploads/{$this->faker->uuid}.{$extension}",
            'processed_path'     => "processed/{$this->faker->uuid}.{$extension}",
            'status'             => $this->faker->randomElement(FileStatusEnum::cases()),
            'processing_options' => [
                'operation' => $this->faker->randomElement(['crop', 'resize', 'normalize']),
                'width'     => $this->faker->numberBetween(100, 1920),
                'height'    => $this->faker->numberBetween(100, 1080),
                'quality'   => $this->faker->numberBetween(60, 100),
                'crop'      => $this->faker->boolean(),
            ],
            'error_message'      => fn(array $attributes) => $attributes['status'] === FileStatusEnum::FAILED
                ? $this->faker->sentence()
                : null,
            'processed_at'       => fn(array $attributes) => in_array($attributes['status'], ['completed', 'failed'])
                ? $this->faker->dateTimeThisMonth()
                : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => FileStatusEnum::PENDING,
            'processed_path' => null,
            'error_message'  => null,
            'processed_at'   => null,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => FileStatusEnum::PROCESSING,
            'processed_path' => null,
            'error_message'  => null,
            'processed_at'   => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => FileStatusEnum::COMPLETED,
            'processed_path' => sprintf("processed/%s.%s", $this->faker->uuid, pathinfo($attributes['original_path'], PATHINFO_EXTENSION)),
            'error_message'  => null,
            'processed_at'   => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => FileStatusEnum::FAILED,
            'processed_path' => null,
            'error_message'  => $this->faker->sentence(),
            'processed_at'   => now(),
        ]);
    }

    // Можно продолжать:
    // public function withOperation(){}
    // public function withDimensions(){}
    // public function withQuality(){}
    // public function withCrop(){}
}
