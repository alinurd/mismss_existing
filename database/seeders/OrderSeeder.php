<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $length = 30;

        for($i=0;$i<$length;$i++){

            $custId = $i<=15 ? "000022" : "000018";
            $custTypeId = $i<=15 ? "IND" : "COR";

            DB::table("order_list")->insert([
                "cust_id" => "",
                "cust_type_id" => $custTypeId,
                "order_status_id" => "READY",
                "invoice_id" => "INV/AJV/23".fake()->randomNumber,
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
                "email" => fake()->email,
                "second_name" => fake()->name,
                "second_phone" => fake()->phoneNumber
            ]);

        }
    }
}
