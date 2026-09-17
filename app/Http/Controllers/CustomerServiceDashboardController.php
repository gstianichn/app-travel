<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerServiceDashboardController extends Controller
{
    /**
     * Dashboard Customer Service
     */
    public function index()
    {
        return view('dashboard.customer-service.index');
    }

    /**
     * Halaman Live Chat
     */
    public function liveChat()
    {
        return view('dashboard.customer-service.live-chat');
    }
}
