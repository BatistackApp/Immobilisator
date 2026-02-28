<?php

namespace Database\Factories;

use App\Enums\InterventionType;
use App\Models\Asset;
use App\Models\Intervention;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InterventionFactory extends Factory
{
    protected $model = Intervention::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(InterventionType::cases()),
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'cost' => $this->faker->randomFloat(),
            'intervention_date' => Carbon::now(),
            'is_capitalized' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'asset_id' => Asset::factory(),
            'provider_id' => Provider::factory(),
        ];
    }
}
