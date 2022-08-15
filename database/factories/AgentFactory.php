<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = 'My trade';
        return [
            'name' => $name,
            'key' => bcrypt($name . time()),
            'ip' => '127.0.0.1'
        ];
    }
}
