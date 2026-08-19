<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AnnouncerService
{
    protected $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }
}