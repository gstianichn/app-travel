<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerServiceDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.customer-service.index');
    }
}