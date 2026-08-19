<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $length = 10;

        for($i=0;$i<$length;$i++){

            $custId = $i<=5 ? "IND" : "COR";

            DB::table("cust_list")->insert([
                "id" => fake()->text(5),
                "cust_type_id" => "COR",
                "created_at" => fake()->date." ".fake()->time,
                "created_by" => fake()->name("6"),
                "updated_at" => fake()->date." ".fake()->time,
                "updated_by" => fake()->name("6"),
                "first_name" => fake()->name("5"),
                "middle_name" => fake()->name("5"),
                "last_name" => fake()->name("5"),
                "phone" => fake()->phoneNumber,
                "address" => fake()->address,
                "sub_district" => fake()->city,
                "district" => fake()->city,
                "city" => fake()->city,
                "prov" => fake()->state,
                "postal_code" => fake()->postcode,
                "postal_code" => fake()->email,
            ]);

        }
    }
}
