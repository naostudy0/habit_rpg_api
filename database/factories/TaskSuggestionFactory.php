<?php

namespace Database\Factories;

use App\Models\TaskSuggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskSuggestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = TaskSuggestion::class;

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
            'memo' => fake()->sentence(),
        ];
    }
}
