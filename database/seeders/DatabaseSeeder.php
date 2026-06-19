<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => config('services.admin.email')],
            [
                'name' => 'Admin PuSaKap',
                'password' => bcrypt(config('services.admin.password')),
            ]
        );

        $this->call(DataSeeder::class);
    }
}
