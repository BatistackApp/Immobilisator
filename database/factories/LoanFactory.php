<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'principal_amount' => $this->faker->randomFloat(),
            'interest_rate' => $this->faker->randomFloat(),
            'duration_months' => $this->faker->randomNumber(),
            'first_installment_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_id' => Asset::factory(),
            'provider_id' => Provider::factory(),
        ];
    }
}
