<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteList extends Model
{
    use HasFactory;

    protected $table = 'route_list';
    protected $primarykey = 'id';
    public $incrementing = false;
}
