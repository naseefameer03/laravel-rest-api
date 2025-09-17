<?php

namespace App\Http\Controllers;

use App\Services\ApiService;

abstract class Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }
}
