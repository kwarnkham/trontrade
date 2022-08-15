<?php

namespace Database\Factories;

use App\Models\OtpAbility;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtpAbilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OtpAbility::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name()
        ];
    }
}
