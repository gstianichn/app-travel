<?php

namespace App\Http\Controllers;

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

    /**
     * Halaman Melihat Konteks Booking
     */
    public function bookingContext()
    {
        return view('dashboard.customer-service.booking-context');
    }

    /**
     * Halaman Melihat Riwayat Chat
     */
    public function chatHistory()
    {
        return view('dashboard.customer-service.chat-history');
    }

    /**
     * Halaman Mengirim Gambar / File & Generator Tiket Instan
     */
    public function sendFile()
    {
        return view('dashboard.customer-service.send-file');
    }

    /**
     * Halaman Melakukan Eskalasi ke Admin & Pelacakan Status
     */
    public function escalation()
    {
        return view('dashboard.customer-service.escalation');
    }
}
