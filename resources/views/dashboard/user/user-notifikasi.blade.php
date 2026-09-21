@extends('layouts.dashboard')

@section('page-title', 'Notifikasi')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">3 pesan belum dibaca</p>
        </div>
        <button id="btn-mark-all-read" class="text-xs font-semibold text-blue-600 hover:underline">
            Tandai semua dibaca
        </button>
    </div>

    <!-- GRUP: Hari Ini -->
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">Hari Ini</p>
        <div class="space-y-2.5">

            <!-- Notifikasi 1: Transaksi sukses (Unread) -->
            <div id="notif-1" class="flex gap-3 bg-blue-50 border border-blue-100 rounded-2xl p-4 relative">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex-shrink-0 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-900 leading-snug">Pembayaran Berhasil ✅</p>
                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">Tiket Anda untuk rute <strong>Bandung → Jakarta</strong> hari ini pukul 14:30 WIB sudah dikonfirmasi. Tunjukkan kode booking ke pengemudi.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">09:15 WIB — 21 Sep 2026</p>
                </div>
            </div>

            <!-- Notifikasi 2: Armada mendekat (Unread) -->
            <div id="notif-2" class="flex gap-3 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex-shrink-0 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-900 leading-snug">Armada Mendekat 🚌</p>
                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">Toyota HiAce Premio (B 1234 XYZ) dengan Sopir <strong>Pak Budi</strong> akan tiba di Dipatiukur ±15 menit lagi. Harap bersiap di lokasi penjemputan.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">14:15 WIB — 21 Sep 2026</p>
                </div>
            </div>

            <!-- Notifikasi 3: Pengingat keberangkatan (Unread) -->
            <div id="notif-3" class="flex gap-3 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex-shrink-0 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22c.55 0 1-.45 1-1h-2c0 .55.45 1 1 1zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-900 leading-snug">Pengingat Keberangkatan ⏰</p>
                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">Perjalanan Anda ke Jakarta berangkat 1 jam lagi! Segera menuju titik penjemputan di <strong>Dipatiukur</strong>.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">13:30 WIB — 21 Sep 2026</p>
                </div>
            </div>

        </div>
    </div>

    <!-- GRUP: Kemarin -->
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">Kemarin</p>
        <div class="space-y-2.5">

            <!-- Notifikasi: Status refund -->
            <div id="notif-4" class="flex gap-3 bg-white border border-slate-200 rounded-2xl p-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex-shrink-0 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6 6-6"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 leading-snug">Refund Disetujui 💚</p>
                    <p class="text-xs text-slate-500 mt-0.5">Pengajuan refund Anda untuk tiket <strong>INV-20260814-02</strong> telah disetujui. Dana Rp 99.000 akan dikembalikan dalam 1–3 hari kerja.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">10:22 WIB — 20 Sep 2026</p>
                </div>
            </div>

            <!-- Notifikasi: Voucher baru -->
            <div id="notif-5" class="flex gap-3 bg-white border border-slate-200 rounded-2xl p-4">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex-shrink-0 flex items-center justify-center text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 9h.01M15 15h.01M16 8l-8 8M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 leading-snug">Voucher Baru Tersedia 🎁</p>
                    <p class="text-xs text-slate-500 mt-0.5">Selamat! Anda mendapatkan voucher <strong>HEMATLIBURAN</strong> diskon 25% untuk perjalanan berikutnya. Berlaku hingga 30 Des 2026.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">08:00 WIB — 20 Sep 2026</p>
                </div>
            </div>

        </div>
    </div>

    <!-- GRUP: Lebih Lama -->
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">Lebih Lama</p>
        <div class="space-y-2.5">

            <!-- Notifikasi: Pembaruan sistem -->
            <div id="notif-6" class="flex gap-3 bg-white border border-slate-200 rounded-2xl p-4">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex-shrink-0 flex items-center justify-center text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 leading-snug">Pembaruan Sistem ℹ️</p>
                    <p class="text-xs text-slate-500 mt-0.5">Kami telah memperbarui fitur e-ticket dan tampilan aplikasi untuk pengalaman yang lebih baik. Terima kasih atas kepercayaan Anda.</p>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">15 Sep 2026</p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
