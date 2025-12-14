<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'is_dark_mode' => false,
            'is_24_hour_format' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * ダークモードを有効にする
     */
    public function darkMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_dark_mode' => true,
        ]);
    }

    /**
     * 12時間形式に設定する
     */
    public function twelveHourFormat(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_24_hour_format' => false,
        ]);
    }
}
