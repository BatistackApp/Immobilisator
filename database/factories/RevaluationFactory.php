<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Revaluation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RevaluationFactory extends Factory
{
    protected $model = Revaluation::class;

    public function definition(): array
    {
        return [
            'fair_value' => $this->faker->randomFloat(),
            'previous_vnc' => $this->faker->randomFloat(),
            'gap_amount' => $this->faker->randomFloat(),
            'expert_name' => $this->faker->company,
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'revaluation_date' => $this->faker->date(),

            'asset_id' => Asset::factory(),
        ];
    }
}
