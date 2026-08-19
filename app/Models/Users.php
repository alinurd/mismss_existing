<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primarykey = 'id';
    public $timestamps = true;
    
    protected $fillable = ['status_id','updated_by','created_by','role_id','rank','username','password','fullname','phone','address','email'];
}
