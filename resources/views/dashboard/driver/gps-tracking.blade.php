@extends('layouts.driver')

@section('title', 'GPS Live Tracking - Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; min-height: 38px; padding: 0 16px;">
            <span>&larr;</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 12. FITUR GPS TRACKING SAAT PERJALANAN AKTIF --}}
    @include('dashboard.driver.partials.gps-tracking')
@endsection
