<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'filename' => $this->faker->word() . '.pdf',
            'filepath' => '/path/to/file/' . $this->faker->uuid() . '.pdf',
            'filesize' => $this->faker->numberBetween(1000, 1000000), // filesize in bytes
            'filetype' => 'application/pdf',
        ];
    }
}