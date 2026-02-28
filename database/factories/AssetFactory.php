<?php

namespace Database\Factories;

use App\Enums\AmortizationMethod;
use App\Enums\AssetStatus;
use App\Enums\FundingType;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'reference' => 'IMM-PEST-'.$this->faker->numerify('####'),
            'designation' => $this->faker->word(),
            'funding_type' => $this->faker->randomElement(FundingType::cases()),
            'acquisition_value' => $this->faker->randomFloat(),
            'salvage_value' => $this->faker->randomFloat(),
            'acquisition_date' => Carbon::now(),
            'service_date' => Carbon::now(),
            'useful_life' => $this->faker->randomNumber(),
            'gross_value_opening' => $this->faker->randomFloat(),
            'accumulated_depreciation_opening' => $this->faker->randomFloat(),
            'depreciable_basis' => $this->faker->randomFloat(),
            'amortization_method' => $this->faker->randomElement(AmortizationMethod::cases()),
            'status' => $this->faker->randomElement(AssetStatus::cases()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_category_id' => AssetCategory::factory(),
            'location_id' => Location::factory(),
            'provider_id' => Provider::factory(),
        ];
    }
}
