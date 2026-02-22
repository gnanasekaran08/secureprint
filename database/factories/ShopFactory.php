<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'       => $this->faker->uuid(),
            'name'       => $this->faker->company(),
            'user_id'    => 2,
            'created_by' => 1, // Assuming a default user ID for created_by
            'updated_by' => 1, // Assuming no updates initially
        ];
    }
}
