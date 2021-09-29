<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::all();
        DB::table('currencies')->insert([
           
            [
                'country_id' => $countries[0]->id,
                'currency_name' => "US Dollar",
                'currency_code' => "USD",
                'is_base_currency' => 1,
                'currency_sym' => "&dollar;",
                "base_rate" => 1
            ],
            [
                'country_id' => $countries[1]->id,
                'currency_name' => "Naira",
                'currency_code' => "NG",
                'is_base_currency' => 0,
                'currency_sym' => "&#8358;",
                "base_rate" => 541
            ],
            [
                'country_id' => $countries[2]->id,
                'currency_name' => "Pounds",
                'currency_code' => "Pounds",
                'is_base_currency' => 0,
                'currency_sym' => "&pound;",
                "base_rate" => 0.72
            ]

        ]);
    }
}
