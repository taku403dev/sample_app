<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Crypt::encryptString($this->faker->name()),
            'price' => Crypt::encryptString((string)$this->faker->unixTime()),
            'info' => Crypt::encryptString($this->faker->text()),
        ];
    }
}
