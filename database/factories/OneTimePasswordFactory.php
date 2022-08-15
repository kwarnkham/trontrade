<?php

namespace Database\Factories;

use App\Models\OneTimePassword;
use Illuminate\Database\Eloquent\Factories\Factory;

class OneTimePasswordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OneTimePassword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
