<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'reference' => $this->faker->word(),
            'designation' => $this->faker->word(),
            'funding_type' => $this->faker->word(),
            'acquisition_value' => $this->faker->randomFloat(),
            'salvage_value' => $this->faker->randomFloat(),
            'acquisition_date' => Carbon::now(),
            'service_date' => Carbon::now(),
            'useful_life' => $this->faker->randomNumber(),
            'gross_value_opening' => $this->faker->randomFloat(),
            'accumulated_depreciation_opening' => $this->faker->randomFloat(),
            'amortization_method' => $this->faker->word(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_category_id' => AssetCategory::factory(),
            'location_id' => Location::factory(),
        ];
    }
}
