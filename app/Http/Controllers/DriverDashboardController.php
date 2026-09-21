<?php

namespace App\Http\Controllers;

class DriverDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.driver.index');
    }
}
