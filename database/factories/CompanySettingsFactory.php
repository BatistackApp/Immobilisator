<?php

namespace Database\Factories;

use App\Models\CompanySettings;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CompanySettingsFactory extends Factory
{
    protected $model = CompanySettings::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->name(),
            'vat_number' => $this->faker->word(),
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'fiscal_year_start_month' => $this->faker->randomNumber(),
            'amortization_options' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
