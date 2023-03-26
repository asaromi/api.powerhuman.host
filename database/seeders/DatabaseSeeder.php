<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(2)->unverified()->create();
        \App\Models\Company::factory(5)->create();
        \App\Models\UserCompany::factory(5)->create();

        $this->call([
            RoleSeeder::class,
            TeamSeeder::class,
        ]);

        \App\Models\Employee::factory(100)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
