<?php

namespace Database\Factories;

use App\Http\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
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
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            // `username`/`email` are unique in the schema. faker's unique() pool
            // (e.g. userName) is small and its state persists across tests in one
            // process, so it overflows/collides on large batches (30 seeded users
            // + many per-test users) and makes the suite flaky. Append a random
            // token instead for guaranteed, order-independent uniqueness.
            // 12-char random token: unique across the 30 seeded + per-test users,
            // and stays within the 5..20 username validation bounds.
            'username' => Str::random(12),
            'password' => bcrypt('test123'),
            'role_id' => 2,
            'city' => '-',
            'country' => $this->faker->country,
            'email' => Str::random(12).'@example.com',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
