@extends('layouts.admin')

@section('title', 'Eskalasi Customer Service')
@section('page-title', 'Eskalasi Customer Service')
@section('page-subtitle', 'Pesan eskalasi aktif dari tim CS yang perlu ditindaklanjuti, beserta tabel riwayat eskalasi yang sudah selesai.')

@section('content')
<style>
    .esc-page { display: flex; flex-direction: column; gap: 22px; }
    .esc-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .esc-stat { background: #fff; border: 1px solid #d4dfda; border-radius: 14px; padding: 16px 20px; }
    .esc-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #5c6862; }
    .esc-stat-value { font-size: 26px; font-weight: 800; color: #17221d; margin-top: 4px; }

    /* Search bar */
    .esc-search-bar { background: #fff; border: 1px solid #d4dfda; border-radius: 14px; padding: 14px 18px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .esc-search-bar input { flex: 1 1 260px; height: 38px; padding: 0 12px; border: 1px solid #d4dfda; border-radius: 8px; font-size: 13px; background: #fbfdfc; }
    .esc-btn { height: 38px; padding: 0 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .esc-btn-primary { background: #087f55; color: #fff; }
    .esc-btn-primary:hover { background: #066b47; }
    .esc-btn-outline { background: #fff; border: 1px solid #d4dfda; color: #4b5852; }
    .esc-btn-outline:hover { background: #f1f5f3; color: #17221d; }

    /* Inbox / Message layout for Open Escalations */
    .esc-inbox { display: grid; grid-template-columns: 320px minmax(0, 1fr); background: #fff; border: 1px solid #d4dfda; border-radius: 14px; overflow: hidden; min-height: 400px; }
    .esc-inbox-list { border-right: 1px solid #e2eae6; display: flex; flex-direction: column; max-height: 520px; overflow-y: auto; }
    .esc-inbox-list-head { padding: 14px 16px; border-bottom: 1px solid #e2eae6; background: #f8faf9; font-size: 13px; font-weight: 800; color: #17221d; display: flex; justify-content: space-between; align-items: center; }
    .esc-inbox-item { display: block; padding: 14px 16px; border-bottom: 1px solid #edf2ef; text-decoration: none; color: inherit; transition: background 0.15s; }
    .esc-inbox-item:hover { background: #f6f9f7; }
    .esc-inbox-item.is-active { background: #eef6f1; border-left: 4px solid #087f55; }
    .esc-inbox-item-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
    .esc-inbox-name { font-size: 13px; font-weight: 700; color: #17221d; }
    .esc-inbox-time { font-size: 11px; color: #68766f; white-space: nowrap; }
    .esc-inbox-code { font-size: 11px; font-family: monospace; color: #087f55; font-weight: 700; margin-top: 2px; }
    .esc-inbox-snippet { font-size: 12px; color: #5c6862; margin-top: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .esc-thread-pane { display: flex; flex-direction: column; background: #fbfdfc; }
    .esc-thread-head { padding: 16px 20px; border-bottom: 1px solid #e2eae6; background: #fff; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 10px; }
    .esc-thread-body { flex: 1; padding: 20px; display: flex; flex-direction: column; gap: 14px; overflow-y: auto; }
    .esc-bubble { max-width: 88%; border-radius: 14px; padding: 14px 16px; font-size: 13px; line-height: 1.5; }
    .esc-bubble-cs { align-self: flex-start; background: #fff; border: 1px solid #d4dfda; border-bottom-left-radius: 4px; color: #17221d; }
    .esc-bubble-admin { align-self: flex-end; background: #eef6f1; border: 1px solid #b8dfc9; border-bottom-right-radius: 4px; color: #0c392f; }
    .esc-bubble-meta { font-size: 11px; font-weight: 700; color: #5c6862; margin-bottom: 6px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .esc-thread-composer { padding: 16px 20px; border-top: 1px solid #e2eae6; background: #fff; display: flex; flex-direction: column; gap: 10px; }
    .esc-thread-composer textarea { width: 100%; border: 1px solid #d4dfda; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-family: inherit; resize: vertical; }
    .esc-thread-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; }

    /* Resolved History Table */
    .esc-table-card { background: #fff; border: 1px solid #d4dfda; border-radius: 14px; overflow: hidden; }
    .esc-table-head { padding: 16px 20px; border-bottom: 1px solid #e2eae6; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
    .esc-table-head h3 { font-size: 15px; font-weight: 800; color: #17221d; margin: 0; }
    .esc-table-wrap { width: 100%; overflow-x: auto; }
    .esc-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .esc-table th { background: #f8faf9; padding: 12px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #5c6862; border-bottom: 1px solid #e2eae6; white-space: nowrap; }
    .esc-table td { padding: 14px 16px; border-bottom: 1px solid #edf2ef; vertical-align: top; color: #17221d; }
    .esc-table tr:last-child td { border-bottom: none; }
    .esc-badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .esc-badge-open { background: #f3e8ff; color: #6b21a8; }
    .esc-badge-done { background: #dcfce7; color: #166534; }
    .esc-badge-cat { background: #fff7ed; color: #9a3412; }

    @media (max-width: 960px) {
        .esc-inbox { grid-template-columns: 1fr; }
        .esc-inbox-list { border-right: none; border-bottom: 1px solid #e2eae6; max-height: 260px; }
        .esc-stats { grid-template-columns: 1fr; }
    }
</style>

<div class="esc-page">
    @if (session('success'))
        <div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistik --}}
    <div class="esc-stats">
        <div class="esc-stat">
            <div class="esc-stat-label">Pesan Perlu Ditangani</div>
            <div class="esc-stat-value" style="color:#7c3aed;">{{ $stats['open'] }}</div>
        </div>
        <div class="esc-stat">
            <div class="esc-stat-label">Eskalasi Selesai</div>
            <div class="esc-stat-value" style="color:#087f55;">{{ $stats['resolved'] }}</div>
        </div>
        <div class="esc-stat">
            <div class="esc-stat-label">Total Eskalasi</div>
            <div class="esc-stat-value">{{ $stats['total'] }}</div>
        </div>
    </div>

    {{-- Pencarian --}}
    <form method="GET" action="{{ route('dashboard.admin.eskalasi.index') }}" class="esc-search-bar">
        <input type="search" name="q" value="{{ $search }}" placeholder="Cari kode tiket (KDL-...), kode booking, nama pelanggan, atau isi pesan...">
        <button type="submit" class="esc-btn esc-btn-primary">Cari</button>
        @if ($search !== '')
            <a href="{{ route('dashboard.admin.eskalasi.index') }}" class="esc-btn esc-btn-outline">Reset</a>
        @endif
    </form>

    {{-- 1. PESAN ESKALASI MASUK (TAMPILAN INBOX / PESAN) --}}
    <section class="esc-inbox" aria-label="Pesan Eskalasi Masuk">
        <div class="esc-inbox-list">
            <div class="esc-inbox-list-head">
                <span>Pesan Eskalasi Masuk</span>
                <span class="esc-badge esc-badge-open">{{ $openEscalations->count() }}</span>
            </div>

            @forelse ($openEscalations as $item)
                @php $isActive = $activeEscalation && $activeEscalation->id === $item->id; @endphp
                <a href="{{ route('dashboard.admin.eskalasi.index', array_filter(['ticket' => $item->id, 'q' => $search])) }}"
                   class="esc-inbox-item {{ $isActive ? 'is-active' : '' }}">
                    <div class="esc-inbox-item-top">
                        <span class="esc-inbox-name">{{ $item->customer_name ?: 'Pelanggan' }}</span>
                        <span class="esc-inbox-time">{{ $item->escalated_at?->format('H:i') ?? $item->created_at?->format('H:i') }}</span>
                    </div>
                    <div class="esc-inbox-code">
                        {{ $item->ticket_code }} @if ($item->booking_code) &middot; {{ $item->booking_code }} @endif
                    </div>
                    <div class="esc-inbox-snippet">
                        {{ $item->escalation_reason ?: $item->description }}
                    </div>
                </a>
            @empty
                <div style="padding:28px 18px; text-align:center; font-size:12.5px; color:#5c6862;">
                    <strong style="color:#17221d; display:block; margin-bottom:4px;">Tidak ada pesan eskalasi aktif</strong>
                    Semua eskalasi dari Customer Service sudah selesai ditangani.
                </div>
            @endforelse
        </div>

        <div class="esc-thread-pane">
            @if ($activeEscalation)
                <div class="esc-thread-head">
                    <div>
                        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                            <strong style="font-size:15px; color:#17221d;">{{ $activeEscalation->customer_name ?: 'Pelanggan' }}</strong>
                            <span class="esc-badge esc-badge-cat">{{ $activeEscalation->categoryLabel() }}</span>
                            <span class="esc-badge esc-badge-open">{{ $activeEscalation->statusLabel() }}</span>
                        </div>
                        <div style="font-size:12px; color:#5c6862; margin-top:3px;">
                            Tiket <strong style="font-family:monospace;">{{ $activeEscalation->ticket_code }}</strong>
                            @if ($activeEscalation->booking_code)
                                &middot; Booking <strong style="font-family:monospace;">{{ $activeEscalation->booking_code }}</strong>
                            @endif
                            @if ($activeEscalation->customer_phone)
                                &middot; HP {{ $activeEscalation->customer_phone }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="esc-thread-body">
                    {{-- Pesan Eskalasi dari CS --}}
                    <div class="esc-bubble esc-bubble-cs">
                        <div class="esc-bubble-meta">
                            <span>Dari CS: {{ $activeEscalation->escalatedBy?->name ?? $activeEscalation->reporter?->name ?? 'Customer Service' }}</span>
                            <span>&middot;</span>
                            <span>{{ $activeEscalation->escalated_at?->translatedFormat('d M Y, H:i') ?? $activeEscalation->created_at?->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <p style="margin:0 0 8px;"><strong>Alasan Eskalasi:</strong> {{ $activeEscalation->escalation_reason ?: '-' }}</p>
                        <p style="margin:0; color:#4b5852;"><strong>Kronologi:</strong> {{ $activeEscalation->description }}</p>
                    </div>

                    {{-- Catatan Admin Sebelumnya (jika sudah pernah disimpan) --}}
                    @if (! empty($activeEscalation->follow_up_notes))
                        <div class="esc-bubble esc-bubble-admin">
                            <div class="esc-bubble-meta" style="color:#0c5a3e;">
                                <span>Catatan Tindak Lanjut Admin</span>
                            </div>
                            <p style="margin:0; white-space:pre-line;">{{ $activeEscalation->follow_up_notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Form Tanggapan & Tandai Selesai --}}
                <form method="POST" action="{{ route('dashboard.admin.eskalasi.update', $activeEscalation) }}" class="esc-thread-composer">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="redirect_q" value="{{ $search }}">

                    <label for="admin-esc-notes" style="font-size:11px; font-weight:700; text-transform:uppercase; color:#4b5852;">
                        Balasan / Keputusan Admin
                    </label>
                    <textarea id="admin-esc-notes"
                              name="follow_up_notes"
                              rows="2"
                              placeholder="Tuliskan keputusan atau tindak lanjut Admin (mis. refund disetujui, armada pengganti dikirim)...">{{ $activeEscalation->follow_up_notes }}</textarea>

                    <div class="esc-thread-actions">
                        <button type="submit" name="status" value="sedang_dicek" class="esc-btn esc-btn-outline">
                            Simpan &amp; Sedang Diproses
                        </button>
                        <button type="submit" name="status" value="selesai" class="esc-btn esc-btn-primary">
                            Tandai Selesai
                        </button>
                    </div>
                </form>
            @else
                <div style="margin:auto; padding:40px 24px; text-align:center; color:#5c6862;">
                    <strong style="color:#17221d; font-size:15px;">Semua pesan eskalasi sudah selesai</strong>
                    <p style="font-size:13px; margin-top:4px;">Lihat riwayat keputusan Admin pada tabel di bawah.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- 2. RIWAYAT ESKALASI SELESAI (TABEL DATA RINGKAS) --}}
    <section class="esc-table-card" aria-label="Riwayat Eskalasi Selesai">
        <div class="esc-table-head">
            <h3>Riwayat Eskalasi Selesai</h3>
            <span class="esc-badge esc-badge-done">{{ $resolvedEscalations->total() }} Selesai</span>
        </div>

        @if ($resolvedEscalations->isEmpty())
            <div style="padding:28px 20px; text-align:center; font-size:13px; color:#5c6862;">
                Belum ada eskalasi yang ditandai selesai.
            </div>
        @else
            <div class="esc-table-wrap">
                <table class="esc-table">
                    <thead>
                        <tr>
                            <th>Kode Tiket &amp; Booking</th>
                            <th>Pelanggan</th>
                            <th>Kategori &amp; Eskalasi CS</th>
                            <th>Keputusan / Catatan Admin</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resolvedEscalations as $done)
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-family:monospace; font-weight:700;">{{ $done->ticket_code }}</div>
                                    @if ($done->booking_code)
                                        <div style="font-family:monospace; font-size:11px; color:#5c6862; margin-top:2px;">{{ $done->booking_code }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:700;">{{ $done->customer_name ?: '-' }}</div>
                                    @if ($done->customer_phone)
                                        <div style="font-size:11.5px; color:#5c6862;">{{ $done->customer_phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="esc-badge esc-badge-cat" style="margin-bottom:4px;">{{ $done->categoryLabel() }}</span>
                                    <div style="font-size:12.5px; color:#17221d;">{{ $done->escalation_reason ?: $done->description }}</div>
                                    <div style="font-size:11px; color:#68766f; margin-top:2px;">
                                        CS: {{ $done->escalatedBy?->name ?? $done->reporter?->name ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:12.5px; color:#0c392f; white-space:pre-line;">{{ $done->follow_up_notes ?: '-' }}</div>
                                </td>
                                <td style="white-space:nowrap; font-size:12px; color:#5c6862;">
                                    {{ $done->resolved_at?->translatedFormat('d M Y, H:i') ?? $done->updated_at?->translatedFormat('d M Y, H:i') }} WIB
                                </td>
                                <td style="white-space:nowrap;">
                                    <span class="esc-badge esc-badge-done">Selesai</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($resolvedEscalations->hasPages())
                <div style="padding:14px 20px; border-top:1px solid #edf2ef;">
                    {{ $resolvedEscalations->links() }}
                </div>
            @endif
        @endif
    </section>
</div>
@endsection
