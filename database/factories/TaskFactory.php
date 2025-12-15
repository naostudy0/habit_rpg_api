<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'scheduled_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'scheduled_time' => fake()->time('H:i:s'),
            'memo' => fake()->optional()->sentence(),
            'is_completed' => false,
        ];
    }

    /**
     * 完了済みの予定にする
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    /**
     * 過去の予定にする
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * 今日の予定にする
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * 明日の予定にする
     */
    public function tomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
        ]);
    }
}
