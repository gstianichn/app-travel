<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
        public function index()
    {
        $user = Auth::user();

        return view('dashboard.admin.index', compact('user'));
    }
}
