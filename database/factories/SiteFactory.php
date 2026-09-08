<?php

namespace Database\Factories;

use App\Enums\SiteStatus;
use App\Enums\SiteType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'name' => fake()->words(2, true),
            'url' => fake()->url(),
            'type' => SiteType::Wordpress,
            'status' => SiteStatus::Normal,
        ];
    }
}
