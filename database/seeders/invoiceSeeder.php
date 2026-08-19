<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class invoiceSeeder extends Seeder
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

            DB::table("data_list")->insert([
                "cust_id" => $custId,
                "cust_type_id" => $custTypeId,
                "warehouse_id" => "SGID",
                "service_id" => "37",
                "created_at" => fake()->date." ".fake()->time,
                "created_by" => fake()->name("6"),
                "updated_at" => fake()->date." ".fake()->time,
                "updated_by" => fake()->name("6"),
                "mismass_order_id" => "000085",
                "mismass_invoice_id" => fake()->randomNumber,
                "mismass_invoice_date" => "",
                "mismass_invoice_link" => "",
                "invoice_status" => "",
                "doku_invoice_id" => "",
                "doku_link" => "",
                "bank_name" => "",
                "bank_account_name" => "",
                "bank_account_id" => "",
                "forwarder_id" => "",
                "forwarder_name" => "",
                "shipping_number" => fake()->randomNumber,
                "shipping_created_at" => fake()->date." ".fake()->time,
                "shipping_created_by" => fake()->name("6"),
                "shipping_updated_at" => fake()->date." ".fake()->time,
                "shipping_updated_by" => fake()->name("6"),
                "length" => "0",
                "width" => "0",
                "height" => "0",
                "weight" => "0",
                "item" => "0",
                "service_name" => "",
                "service_price_per" => "0",
                "packing_price" => "0",
                "packing_desc" => "",
                "import_permit_price" => "0",
                "import_permit_desc" => "",
                "document_price" => "0",
                "document_desc" => "",
                "dr_medicine_price" => "0",
                "dr_medicine_desc" => "",
                "insurance_item_price" => "0",
                "insurance_percent" => "0",
                "insurance_total" => "0",
                "fee_item_price" => "0",
                "fee_percent" => "0",
                "fee_total" => "0",
                "tax_item_price" => "0",
                "tax_percent" => "0",
                "tax_total" => "0",
                "extra_cost_price" => "0",
                "extra_cost_dest" => "",
                "extra_cost_vendor_name" => "",
                "sub_total" => "0",
                "sender_first_name" => fake()->name("5"),
                "sender_middle_name" => fake()->name("5"),
                "sender_last_name" => fake()->name("5"),
                "sender_phone" => fake()->phoneNumber,
                "sender_address" => fake()->address,
                "sender_sub_district" => fake()->city,
                "sender_district" => fake()->city,
                "sender_city" => fake()->city,
                "sender_prov" => fake()->state,
                "sender_postal_code" => fake()->postcode,
                "sender_email" => fake()->email,
                "cons_first_name" => fake()->name("5"),
                "cons_middle_name" => fake()->name("5"),
                "cons_last_name" => fake()->name("5"),
                "cons_phone" => fake()->phoneNumber,
                "cons_address" => fake()->address,
                "cons_sub_district" => fake()->city,
                "cons_district" => fake()->city,
                "cons_city" => fake()->city,
                "cons_prov" => fake()->state,
                "cons_postal_code" => fake()->postcode,
                "cons_email" => fake()->email,
            ]);

        }
    }
}
