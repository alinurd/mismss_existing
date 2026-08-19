<?php
  
namespace App\Imports;
  
use App\Models\Users;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
  
class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Users([
            'status_id' => 1,
            'role_id' => $row['role'],
            'created_by' => "dev8th",
            'updated_by' => "dev8th",
            'rank' => $row['rank'],
            'remember_token' => '',
            'username' => $row['username'],
            'password' => $row['password'],
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'email' => $row['email'],
        ]);
    }
}

?>

