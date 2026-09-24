@extends('layouts.driver')

@section('title', 'Melihat Rute Perjalanan & Waypoints - Cockpit Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="btn-action-ghost" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; font-weight: 700; color: var(--text-muted); background: white; padding: 8px 14px; border-radius: 10px; border: 1px solid var(--border);">
            <span>←</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 6. FITUR MELIHAT RUTE PERJALANAN & WAYPOINTS --}}
    @include('dashboard.driver.partials.rute')
@endsection
