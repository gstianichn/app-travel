@extends('layouts.driver')

@section('title', 'Melihat Riwayat Perjalanan - Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; min-height: 38px; padding: 0 16px;">
            <span>&larr;</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 15. FITUR MELIHAT RIWAYAT PERJALANAN --}}
    @include('dashboard.driver.partials.riwayat')
@endsection
