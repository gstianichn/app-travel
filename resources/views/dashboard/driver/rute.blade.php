@extends('layouts.driver')

@section('title', 'Melihat Rute Perjalanan - Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; min-height: 38px; padding: 0 16px;">
            <span>&larr;</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 6. FITUR MELIHAT RUTE PERJALANAN --}}
    @include('dashboard.driver.partials.rute')
@endsection
