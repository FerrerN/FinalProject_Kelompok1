<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellerController
{
    public function index() { return view('seller.dashboard'); }
}
