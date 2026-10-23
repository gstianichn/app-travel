@extends('layouts.driver')

@section('title', 'Melihat Informasi Penumpang - Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="ds-btn ds-btn--secondary" style="font-size: 13px; min-height: 38px; padding: 0 16px;">
            <span>&larr;</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 9. FITUR MELIHAT INFORMASI PENUMPANG --}}
    @include('dashboard.driver.partials.info-penumpang')
@endsection
