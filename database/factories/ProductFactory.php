<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        /******************* LaravelでTDE処理を実施する場合は下記コメントアウトを外す *************/
        return [
            'name' => Crypt::encryptString($this->faker->name()),
            'price' => Crypt::encryptString((string)$this->faker->unixTime()),
            'info' => Crypt::encryptString($this->faker->text()),
        ];
        /**********************************************************************************/

        /******************* DB側でTDE処理を実施する場合は下記コメントアウトを外す ****************/
        // $app_key = env('APP_KEY');
        // $price = (string)$this->faker->unixTime();
        // return [
        //     'name' => DB::raw("HEX(AES_ENCRYPT('{$this->faker->name()}', '{$app_key}'))"),
        //     'price' => DB::raw("HEX(AES_ENCRYPT('{$price}', '{$app_key}'))"),
        //     'info' => DB::raw("HEX(AES_ENCRYPT('{$this->faker->text()}', '{$app_key}'))"),
        // ];
        /*********************************************************************************/
    }
}
