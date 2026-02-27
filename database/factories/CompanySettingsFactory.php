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
            'company_name' => $this->faker->company(),
            'vat_number' => $this->faker->numerify('FR###########'),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->countryCode(),
            'fiscal_year_start_month' => $this->faker->randomNumber(1),
            'amortization_options' => [],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
