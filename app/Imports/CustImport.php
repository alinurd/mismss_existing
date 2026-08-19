<?php
  
namespace App\Imports;
  
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
  
class CustImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Customer([
            'cust_type_id' => "IND",
            'created_by' => "dev8th",
            'updated_by' => "dev8th",
            'first_name' => $row['firstname'] ?? "",
            'middle_name' => $row['middlename'] ?? "",
            'last_name' => $row['lastname'] ?? "",
            'phone' => $row['phone'] ?? "",
            'email'    => $row['email'] ?? "belum_ada_email@mismass.com",
            'address' => $row['address'] ?? "",
            'sub_district' => $row['subdistrict'] ?? "",
            'district' => $row['district'] ?? "",
            'city' => $row['city'] ?? "",
            'prov' => $row['prov'] ?? "",
            'postal_code' => $row['postalcode'] ?? "",
            'reference' => $row['reference'] ?? "",
        ]);
    }
}

?>

