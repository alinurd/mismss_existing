<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiJne extends Model
{
    use HasFactory;
    protected $table = 'jne_webhook_logs';

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    protected $fillable = [
        'created_at',
        'uniq_id',
        'description'
    ];

    public $timestamps = false;
}

?>