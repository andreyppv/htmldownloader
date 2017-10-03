<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Base\Controller;

class AuthorizedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
