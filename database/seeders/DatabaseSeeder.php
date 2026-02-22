<?php
namespace Database\Seeders;

use App\Models\PrintJob;
use App\Models\Shop;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'          => 'Code.',
            'email'         => 'code@dev.io',
            'mobile_number' => '1234567890',
            'type'          => 'admin',
        ]);

        User::factory()->count(10)->create();

        foreach (User::where('type', 'shop')->get() as $user) {
            Shop::factory()
                ->count(rand(1, 3))
                ->create([
                    'user_id' => $user->id,
                ]);
        }

        PrintJob::factory()->count(50)->create()
            ->each(function ($printJob) {
                $printJob->attachments()->createMany(
                    \App\Models\Attachment::factory()->count(2)->make()->toArray()
                );
            });

    }
}
