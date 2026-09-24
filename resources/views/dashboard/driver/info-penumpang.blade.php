@extends('layouts.driver')

@section('title', 'Melihat Informasi Penumpang - Cockpit Driver TravelKu')

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('dashboard.driver.index') }}" class="btn-action-ghost" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; font-weight: 700; color: var(--text-muted); background: white; padding: 8px 14px; border-radius: 10px; border: 1px solid var(--border);">
            <span>←</span>
            <span>Kembali ke Dashboard Utama</span>
        </a>
    </div>

    {{-- 9. FITUR MELIHAT INFORMASI PENUMPANG --}}
    @include('dashboard.driver.partials.info-penumpang')
@endsection
