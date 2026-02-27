<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Leasing;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LeasingFactory extends Factory
{
    protected $model = Leasing::class;

    public function definition(): array
    {
        return [
            'contract_number' => $this->faker->word(),
            'monthly_rent' => $this->faker->randomFloat(),
            'purchase_option_price' => $this->faker->randomFloat(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'option_exercised' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_id' => Asset::factory(),
            'provider_id' => Provider::factory(),
        ];
    }
}
