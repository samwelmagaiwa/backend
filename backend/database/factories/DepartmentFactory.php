<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Department',
            'code' => strtoupper($this->faker->lexify('???')),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'has_divisional_director' => $this->faker->boolean(30), // 30% chance
            'hod_user_id' => null,
            'divisional_director_id' => null,
        ];
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the department has a divisional director.
     */
    public function withDivisionalDirector(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_divisional_director' => true,
        ]);
    }
}