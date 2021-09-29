<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->insert([
           [
            "country_code" => "USA",
            "country_name" => "United States of America",
            "country_tel_code" => "+1",
            "country_logo" => "logos/gooders.png",
            "updated_at" => now(),
            "created_at" => now()
           ],
           [
            "country_code" => "NG",
            "country_name" => "Nigeria",
            "country_tel_code" => "+234",
            "country_logo" => "logos/gooders.png",
            "updated_at" => now(),
            "created_at" => now()
           ],
           [
            "country_code" => "UK",
            "country_name" => "United Kingdom",
            "country_tel_code" => "+44",
            "country_logo" => "logos/gooders.png",
            "updated_at" => now(),
            "created_at" => now()
           ]
           
        ]);
    }
}
