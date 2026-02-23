<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrintJob>
 */
class PrintJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doc_no'          => $this->faker->unique()->numberBetween(100000, 999999),
            'shop_id'         => $this->faker->numberBetween(1, 5),
            'job_uuid'        => $this->faker->uuid(),
            'user_id'         => $this->faker->numberBetween(1, 10),
            'printer'         => $this->faker->randomElement(['Printer A', 'Printer B', 'Printer C']),
            'error_message'   => null,
            'status'          => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'submitted_at'    => $this->faker->dateTimeBetween('-1 week', 'now'),
            'completed_at'    => null,
            'total_cost'      => $this->faker->randomFloat(2, 0.50, 20.00),
            'total_pages'     => $this->faker->numberBetween(1, 100),
            'total_copies'    => $this->faker->numberBetween(1, 5),
            'is_color'        => $this->faker->boolean(),
            'is_double_sided' => $this->faker->boolean(),
            'is_portrait'     => $this->faker->boolean(),
            'otp'             => null,
            'otp_expires_at'  => null,
        ];
    }
}
