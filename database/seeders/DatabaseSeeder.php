<?php

namespace Database\Seeders;

use App\Models\CompanySettings;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $company = CompanySettings::create([
            'company_name' => 'demo',
            'address' => 'Demo Address',
            'postal_code' => '12345',
            'city' => 'Demo City',
            'country' => 'Demo Country',
        ]);

        Artisan::call('make:filament-user', [
            '--name' => 'admin',
            '--email' => 'admin@admin.com',
            '--password' => 'admin',
        ]);

        $company->update(['seeded' => true]);
    }
}
