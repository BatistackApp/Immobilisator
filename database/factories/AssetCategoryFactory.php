<?php

namespace Database\Factories;

use App\Enums\AssetType;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(AssetType::cases()),
            'accounting_code_asset' => 2,
            'accounting_code_depreciation' => '281',
            'accounting_code_provision' => '291',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
