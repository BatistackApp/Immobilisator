<?php

namespace Database\Factories;

use App\Models\AmortizationLine;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AmortizationLineFactory extends Factory
{
    protected $model = AmortizationLine::class;

    public function definition(): array
    {
        return [
            'year' => $this->faker->randomNumber(),
            'base_value' => $this->faker->randomFloat(),
            'annuity_amount' => $this->faker->randomFloat(),
            'accumulated_amount' => $this->faker->randomFloat(),
            'book_value' => $this->faker->randomFloat(),
            'is_posted' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_id' => Asset::factory(),
        ];
    }
}
