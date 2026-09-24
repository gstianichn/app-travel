@extends('layouts.driver')

@section('title', 'Melihat Seat Map Kabin - Cockpit Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="btn-action-ghost" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; font-weight: 700; color: var(--text-muted); background: white; padding: 8px 14px; border-radius: 10px; border: 1px solid var(--border);">
            <span>←</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 5. FITUR MELIHAT SEAT MAP (DENAH KURSI KABIN) --}}
    @include('dashboard.driver.partials.seat-map')
@endsection
