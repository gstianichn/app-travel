@extends('layouts.dashboard')

@section('page-title', 'Notifikasi & Pemberitahuan')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto" x-data="notifikasiApp()">

    <!-- Flash Message -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs">!</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold">✕</button>
        </div>
    @endif

    <!-- Header & Quick Actions -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-extrabold text-slate-900">Pemberitahuan</h1>
                @if($unreadCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                        {{ $unreadCount }} Baru
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Semua terbaca
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Pemberitahuan sistem otomatis: status pembayaran, pengingat perjalanan H-2 jam, dan info operasional armada hari-H.
            </p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('dashboard.user.notifikasi.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- ===== FITUR PENYAMPAIAN GANDA: PUSH NOTIFICATION HP & BROWSER ===== -->
    <div class="rounded-3xl p-5 border text-white relative overflow-hidden shadow-sm"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0 text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                        <path d="M12 2v2"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-200 uppercase tracking-wider">Penyampaian Ganda (Dual Delivery)</span>
                        <span x-show="pushPermission === 'granted'" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            ● Push HP Aktif
                        </span>
                        <span x-show="pushPermission !== 'granted'" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            ● Belum Diaktifkan
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-white mt-0.5">Push Notification HP & Riwayat Database</h3>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl leading-relaxed">
                        Pesan otomatis dikirim melalui Push Notification perangkat HP (FCM/OneSignal & Web Push API) dengan suara/getar sekaligus disimpan rapi di riwayat akun.
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0 self-start sm:self-center">
                <button type="button" @click="requestPushPermission()"
                        x-show="pushPermission !== 'granted'"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                    <span>Aktifkan Push Notifikasi HP</span>
                </button>

                <div x-show="pushPermission === 'granted'" class="flex items-center gap-2 text-xs font-semibold text-emerald-400 bg-white/5 border border-white/10 px-3.5 py-2 rounded-xl">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span>Perangkat Terhubung</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PANEL PENGUJIAN: SIMULASI EVENT TRIGGERS ===== -->
    <div class="bg-gradient-to-br from-slate-50 to-blue-50/40 rounded-3xl border border-slate-200 p-5 shadow-2xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Simulasi Event Trigger (Pengujian Langsung)</h4>
            </div>
            <span class="text-[11px] text-slate-500">Klik untuk memicu push notification & riwayat</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <!-- 1. Menunggu Bayar -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="pending">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-amber-50/70 border border-slate-200 hover:border-amber-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-amber-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-amber-100 px-1.5 py-0.5 rounded text-amber-800">15 Menit</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-amber-900 leading-tight">Menunggu Bayar</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Disertai batas waktu bayar</p>
                </button>
            </form>

            <!-- 2. Pembayaran Sukses -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="confirmed">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-emerald-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-emerald-100 px-1.5 py-0.5 rounded text-emerald-800">E-Ticket</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-emerald-900 leading-tight">Pembayaran Sukses</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">E-Ticket QR aktif</p>
                </button>
            </form>

            <!-- 3. Transaksi Dibatalkan Waktu Habis -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="expired">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-rose-50/70 border border-slate-200 hover:border-rose-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-rose-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-rose-100 px-1.5 py-0.5 rounded text-rose-800">Batal</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-rose-900 leading-tight">Waktu Habis</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Transaksi dibatalkan</p>
                </button>
            </form>

            <!-- 4. Pengingat Perjalanan H-2 Jam -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="reminder_2h">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-blue-50/70 border border-slate-200 hover:border-blue-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-blue-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">H-2 Jam</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-blue-900 leading-tight">Pengingat Perjalanan</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Rute, jam tiba pool & kursi</p>
                </button>
            </form>

            <!-- 5. Info Operasional Armada Hari-H -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="operational_vehicle">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-indigo-50/70 border border-slate-200 hover:border-indigo-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-indigo-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-indigo-100 px-1.5 py-0.5 rounded text-indigo-800">Hari-H</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-900 leading-tight">Info Pelat Minibus</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Pelat nomor & gate pool</p>
                </button>
            </form>

            <!-- 6. Info Keterlambatan Kondisi Jalan -->
            <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="operational_delay">
                <button type="submit" class="w-full text-left p-3 rounded-2xl bg-white hover:bg-amber-50/70 border border-slate-200 hover:border-amber-300 transition group shadow-2xs">
                    <div class="flex items-center justify-between text-amber-600 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span class="text-[10px] font-bold uppercase bg-amber-100 px-1.5 py-0.5 rounded text-amber-800">+20 Mnt</span>
                    </div>
                    <p class="text-xs font-bold text-slate-800 group-hover:text-amber-900 leading-tight">Kondisi Jalan / Delay</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Update macet tol / cuaca</p>
                </button>
            </form>
        </div>
    </div>

    <!-- ===== TAB FILTER KATEGORI ===== -->
    <div class="flex items-center gap-1.5 p-1.5 bg-slate-100 rounded-2xl text-xs overflow-x-auto">
        <a href="{{ route('dashboard.user.user-notifikasi') }}"
           class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 flex-shrink-0 {{ empty($selectedCategory) ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($selectedCategory) ? 'bg-slate-100 text-slate-700' : 'bg-slate-200 text-slate-600' }}">
                {{ $notifications->total() }}
            </span>
        </a>

        <a href="{{ route('dashboard.user.user-notifikasi', ['category' => 'transaksi']) }}"
           class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 flex-shrink-0 {{ $selectedCategory === 'transaksi' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            <span>Status Transaksi</span>
            @if($countTransaksi > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-700">{{ $countTransaksi }}</span>
            @endif
        </a>

        <a href="{{ route('dashboard.user.user-notifikasi', ['category' => 'pengingat']) }}"
           class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 flex-shrink-0 {{ $selectedCategory === 'pengingat' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Pengingat (H-2 Jam)</span>
            @if($countPengingat > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-700">{{ $countPengingat }}</span>
            @endif
        </a>

        <a href="{{ route('dashboard.user.user-notifikasi', ['category' => 'operasional']) }}"
           class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 flex-shrink-0 {{ $selectedCategory === 'operasional' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <span>Info Operasional</span>
            @if($countOperasional > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-indigo-100 text-indigo-700">{{ $countOperasional }}</span>
            @endif
        </a>
    </div>

    <!-- ===== DAFTAR RIWAYAT NOTIFIKASI DATABASE ===== -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $data = $notif->data;
                $category = $data['category'] ?? 'system';
                $statusType = $data['status_type'] ?? '';
                $isUnread = $notif->unread();
                $deepLink = route('dashboard.user.notifikasi.open', $notif->id);
                $actionLabel = $data['action_label'] ?? 'Buka Halaman Terkait';

                // Tentukan warna tema per kategori
                $theme = match($category) {
                    'transaksi' => match($statusType) {
                        'confirmed', 'paid' => [
                            'border' => $isUnread ? 'border-emerald-300 ring-2 ring-emerald-50' : 'border-slate-200',
                            'bg' => $isUnread ? 'bg-emerald-50/50' : 'bg-white',
                            'icon_bg' => 'bg-emerald-100 text-emerald-600',
                            'badge' => 'bg-emerald-100 text-emerald-800',
                            'badge_text' => 'Transaksi Sukses',
                            'btn' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
                        ],
                        'expired', 'cancelled' => [
                            'border' => $isUnread ? 'border-rose-300 ring-2 ring-rose-50' : 'border-slate-200',
                            'bg' => $isUnread ? 'bg-rose-50/50' : 'bg-white',
                            'icon_bg' => 'bg-rose-100 text-rose-600',
                            'badge' => 'bg-rose-100 text-rose-800',
                            'badge_text' => 'Waktu Habis / Batal',
                            'btn' => 'bg-rose-600 hover:bg-rose-700 text-white',
                        ],
                        default => [
                            'border' => $isUnread ? 'border-amber-300 ring-2 ring-amber-50' : 'border-slate-200',
                            'bg' => $isUnread ? 'bg-amber-50/50' : 'bg-white',
                            'icon_bg' => 'bg-amber-100 text-amber-600',
                            'badge' => 'bg-amber-100 text-amber-800',
                            'badge_text' => 'Menunggu Pembayaran',
                            'btn' => 'bg-amber-600 hover:bg-amber-700 text-white',
                        ]
                    },
                    'pengingat' => [
                        'border' => $isUnread ? 'border-blue-300 ring-2 ring-blue-50' : 'border-slate-200',
                        'bg' => $isUnread ? 'bg-blue-50/50' : 'bg-white',
                        'icon_bg' => 'bg-blue-100 text-blue-600',
                        'badge' => 'bg-blue-100 text-blue-800',
                        'badge_text' => 'Pengingat H-2 Jam',
                        'btn' => 'bg-blue-600 hover:bg-blue-700 text-white',
                    ],
                    'operasional' => [
                        'border' => $isUnread ? 'border-indigo-300 ring-2 ring-indigo-50' : 'border-slate-200',
                        'bg' => $isUnread ? 'bg-indigo-50/50' : 'bg-white',
                        'icon_bg' => 'bg-indigo-100 text-indigo-600',
                        'badge' => 'bg-indigo-100 text-indigo-800',
                        'badge_text' => 'Operasional Hari-H',
                        'btn' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
                    ],
                    default => [
                        'border' => 'border-slate-200',
                        'bg' => 'bg-white',
                        'icon_bg' => 'bg-slate-100 text-slate-600',
                        'badge' => 'bg-slate-100 text-slate-800',
                        'badge_text' => 'Pemberitahuan',
                        'btn' => 'bg-slate-800 hover:bg-slate-900 text-white',
                    ]
                };
            @endphp

            <div class="{{ $theme['bg'] }} border {{ $theme['border'] }} rounded-3xl p-5 shadow-2xs hover:shadow-md transition relative group">
                <div class="flex items-start gap-4">
                    <!-- Icon Bulat -->
                    <div class="w-12 h-12 {{ $theme['icon_bg'] }} rounded-2xl flex-shrink-0 flex items-center justify-center font-bold">
                        @if($category === 'transaksi')
                            @if(in_array($statusType, ['confirmed', 'paid']))
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                            @elseif(in_array($statusType, ['expired', 'cancelled']))
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            @endif
                        @elseif($category === 'pengingat')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                            </svg>
                        @elseif($category === 'operasional')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        @endif
                    </div>

                    <!-- Konten Notifikasi -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $theme['badge'] }}">
                                    {{ $theme['badge_text'] }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">
                                    {{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('H:i') }} WIB)
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                @if($isUnread)
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 ring-4 ring-blue-100" title="Belum dibaca"></span>
                                @endif
                                
                                <!-- Tombol Hapus -->
                                <form action="{{ route('dashboard.user.notifikasi.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition" title="Hapus Notifikasi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Judul & Pesan -->
                        <h3 class="text-sm font-bold text-slate-900 mt-1 leading-snug">
                            {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $data['message'] ?? '-' }}
                        </p>

                        <!-- Highlight Meta Data jika ada -->
                        @if(!empty($data['meta']))
                            <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                                @if(!empty($data['meta']['route']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-semibold">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19L19 4M4 4l15 15"/></svg>
                                        {{ $data['meta']['route'] }}
                                    </span>
                                @endif

                                @if(!empty($data['meta']['pool_arrival_time']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-blue-50 text-blue-800 font-bold border border-blue-200">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Tiba di Pool: {{ $data['meta']['pool_arrival_time'] }} (Wajib)
                                    </span>
                                @endif

                                @if(!empty($data['meta']['seats']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-bold border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        Kursi: {{ $data['meta']['seats'] }}
                                    </span>
                                @endif

                                @if(!empty($data['meta']['plate_number']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-800 text-white font-mono font-bold">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                        {{ $data['meta']['plate_number'] }}
                                    </span>
                                @endif

                                @if(!empty($data['meta']['pickup_location']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                                        {{ $data['meta']['pickup_location'] }}
                                    </span>
                                @endif

                                @if(!empty($data['meta']['delay_minutes']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-100 text-amber-900 font-bold">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Penyesuaian: +{{ $data['meta']['delay_minutes'] }} Menit
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- ===== DEEP LINKING: TOMBOL NAVIGASI LANGSUNG ===== -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <span class="text-[11px] text-slate-400">
                                Ketuk tombol untuk langsung membuka layar terkait:
                            </span>
                            <a href="{{ $deepLink }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold {{ $theme['btn'] }} shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                                <span>{{ $actionLabel }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-12 text-center text-xs text-slate-400 space-y-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto bg-slate-100 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <div>
                    <p class="font-extrabold text-slate-800 text-sm">Tidak Ada Notifikasi</p>
                    <p class="text-slate-500 mt-1">Belum ada riwayat pemberitahuan untuk kategori ini.</p>
                </div>
                <div class="pt-2">
                    <p class="text-[11px] text-slate-400">Gunakan tombol <strong>Simulasi Event Trigger</strong> di atas untuk mencoba pengiriman ganda dan deep linking.</p>
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>

{{-- SCRIPT PENYAMPAIAN GANDA PUSH NOTIFICATION CLIENT-SIDE --}}
<script>
function notifikasiApp() {
    return {
        pushPermission: ('Notification' in window) ? Notification.permission : 'unsupported',

        init() {
            // Periksa antrean push notification dari sesi backend
            @if(session()->has('queued_push_notifications'))
                const queued = @json(session('queued_push_notifications'));
                if (Array.isArray(queued)) {
                    queued.forEach(item => {
                        this.triggerDevicePush(item);
                    });
                }
            @endif
        },

        requestPushPermission() {
            if (!('Notification' in window)) {
                alert('Browser Anda belum mendukung Web Push Notifications.');
                return;
            }

            Notification.requestPermission().then(permission => {
                this.pushPermission = permission;
                if (permission === 'granted') {
                    // Tampilkan notifikasi sambutan
                    new Notification('Push Notifikasi Aktif!', {
                        body: 'Pemberitahuan operasional armada & transaksi TravelKu kini otomatis muncul di layar HP Anda.',
                        icon: '/favicon.ico'
                    });
                } else if (permission === 'denied') {
                    alert('Izin notifikasi ditolak. Anda dapat mengaktifkannya di pengaturan situs browser.');
                }
            });
        },

        triggerDevicePush(item) {
            // Getarkan perangkat mobile jika didukung
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200]);
            }

            if ('Notification' in window && Notification.permission === 'granted') {
                const push = new Notification(item.title, {
                    body: item.body,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    data: {
                        url: item.deep_link_url
                    }
                });

                // Deep Linking: Saat push notification perangkat diklik, langsung arahkan ke URL tujuan
                push.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    if (item.deep_link_url) {
                        window.location.href = item.deep_link_url;
                    }
                };
            }
        }
    }
}
</script>
@endsection
