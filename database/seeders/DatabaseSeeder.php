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
        // User::factory(10)->create();

        User::factory()->create([
            'name'  => 'Dev',
            'email' => 'dev@code.io',
        ]);

        Shop::factory()->count(5)->create();

        PrintJob::factory()->count(50)->create()
            ->each(function ($printJob) {
                $printJob->attachments()->createMany(
                    \App\Models\Attachment::factory()->count(2)->make()->toArray()
                );
            });

    }
}