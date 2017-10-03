<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\AuthorizedController;

class NewsController extends AuthorizedController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('news');
    }
}
