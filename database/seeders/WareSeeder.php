<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $length = 10;

        for($i=0;$i<$length;$i++){

            DB::table("warehouse_list")->insert([
                "id" => fake()->text(5),
                "status_id" => 1,
                "created_at" => fake()->date." ".fake()->time,
                "created_by" => fake()->name("6"),
                "updated_at" => fake()->date." ".fake()->time,
                "updated_by" => fake()->name("6"),
                "name" => fake()->country,
                "location" => fake()->country,
                "description" => fake()->text(50)
            ]);

        }


    }
}
