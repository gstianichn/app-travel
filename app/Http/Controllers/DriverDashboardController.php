<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
        public function index()
    {
        return view('dashboard.driver.index');
    }
}
