<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'manager' => $this->faker->name(),
            'ruc' => $this->generateUniqueRUC(),
            'address' => $this->faker->streetAddress(),
            'phone' => $this->faker->numerify('9########'),
            'email' => $this->faker->unique()->safeEmail(),
            'description' => $this->faker->text(100),
        ];
    }

    protected function generateUniqueRUC(): string
    {
        $prefix = $this->faker->randomElement(['10', '20']);
        $digits = $this->faker->numerify('#########');

        return "$prefix$digits";
    }
}
