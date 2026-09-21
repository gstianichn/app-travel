@extends('layouts.dashboard')

@section('page-title', 'Pembayaran & Verifikasi Gateway')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="paymentHandler({
    remainingSeconds: {{ $remainingPaymentSeconds }},
    bookingId: {{ $booking->id }},
    activeTab: 'va'
})">

    {{-- ===== STEPPER FLOW ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</span>
                Pilih Kursi
            </span>
            <span class="text-slate-300">➔</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</span>
                Data Penumpang & Promo
            </span>
            <span class="text-slate-300">➔</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white font-bold" style="background:#087f55;">
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">3</span>
                Pembayaran
            </span>
        </div>

        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
            Status: Menunggu Pembayaran
        </span>
    </div>

    {{-- ===== BANNER PAYMENT TIMER & ORDER INFO ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kode Booking Transaksi</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <h2 class="text-xl font-mono font-bold text-slate-900">{{ $booking->booking_code }}</h2>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $booking->booking_code }}'); alert('Kode booking disalin!');" class="text-slate-400 hover:text-slate-600 transition" title="Salin Kode Booking">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    No. Invoice: <span class="font-mono font-semibold text-slate-700">{{ $payment->order_id }}</span>
                </p>
            </div>

            {{-- COUNTDOWN TIMER --}}
            <div class="flex flex-col sm:items-end">
                <span class="text-xs font-semibold text-slate-500 mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Batas Waktu Pembayaran (Timer)
                </span>
                <div class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-50 border border-amber-300 text-amber-950">
                    <span class="text-xs font-medium">Sisa Waktu:</span>
                    <span class="font-mono text-xl font-extrabold tracking-wider text-amber-700" x-text="formatTimer(remainingSeconds)"></span>
                </div>
            </div>
        </div>

        {{-- TOTAL JUMLAH BAYAR --}}
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Nominal yang Harus Dibayar:</p>
                <p class="text-2xl font-black text-slate-900 tracking-tight" style="color:#087f55;">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <button type="button" onclick="navigator.clipboard.writeText('{{ (int)$booking->total_price }}'); alert('Nominal Rp {{ number_format($booking->total_price, 0, ',', '.') }} berhasil disalin!');"
                        class="px-4 py-2 text-xs font-bold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Salin Jumlah Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- ===== PILIHAN METODE PEMBAYARAN GATEWAY ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        {{-- TAB SWITCHER METODE --}}
        <div class="flex border-b border-slate-200 bg-slate-50/50 p-2 gap-2 text-xs font-bold">
            <button type="button" @click="activeTab = 'va'"
                    :class="activeTab === 'va' ? 'bg-white text-emerald-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-800'"
                    class="flex-1 py-3 px-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Virtual Account (VA)
            </button>

            <button type="button" @click="activeTab = 'qris'"
                    :class="activeTab === 'qris' ? 'bg-white text-emerald-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-800'"
                    class="flex-1 py-3 px-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                QRIS Dinamis
            </button>

            <button type="button" @click="activeTab = 'ewallet'"
                    :class="activeTab === 'ewallet' ? 'bg-white text-emerald-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-800'"
                    class="flex-1 py-3 px-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                E-Wallet Instant
            </button>
        </div>

        {{-- TAB 1: VIRTUAL ACCOUNT --}}
        <div x-show="activeTab === 'va'" class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center font-bold text-blue-700 text-sm">
                        BCA
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Virtual Account BCA</h4>
                        <p class="text-xs text-slate-400">Verifikasi otomatis 24 jam</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Otomatis Dicek
                </span>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs text-slate-400 font-medium">Nomor Virtual Account:</span>
                    <p class="font-mono text-xl sm:text-2xl font-black text-slate-900 tracking-wider mt-0.5">
                        {{ $payment->account_number }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Nama Akun: <strong>{{ $payment->account_name }}</strong></p>
                </div>
                <div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $payment->account_number }}'); alert('Nomor Virtual Account {{ $payment->account_number }} disalin!');"
                            class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-sm transition flex items-center justify-center gap-2"
                            style="background:#087f55;"
                            onmouseover="this.style.background='#066b47'"
                            onmouseout="this.style.background='#087f55'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Salin No. VA
                    </button>
                </div>
            </div>

            {{-- Instruksi Pembayaran VA --}}
            <div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
                <p class="font-bold text-slate-800">Cara Pembayaran via BCA Mobile / myBCA / ATM:</p>
                <ol class="list-decimal list-inside space-y-1 text-slate-500 pl-1">
                    <li>Buka aplikasi m-BCA atau ATM BCA terdekat.</li>
                    <li>Pilih menu <strong>m-Transfer</strong> ➔ <strong>BCA Virtual Account</strong>.</li>
                    <li>Masukkan nomor Virtual Account di atas: <strong class="text-slate-800">{{ $payment->account_number }}</strong>.</li>
                    <li>Pastikan nama penerima <strong>PT FAST TRAVEL INDONESIA</strong> dan nominal tagihan sesuai.</li>
                    <li>Konfirmasi dan masukkan PIN transaksi Anda. Selesai!</li>
                </ol>
            </div>
        </div>

        {{-- TAB 2: QRIS DINAMIS --}}
        <div x-show="activeTab === 'qris'" class="p-6 text-center space-y-5" style="display: none;">
            <div class="max-w-xs mx-auto space-y-3">
                <div class="inline-block px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
                    QRIS Standar Nasional
                </div>
                <h4 class="font-bold text-slate-900 text-base">Scan QRIS Dinamis</h4>
                <p class="text-xs text-slate-500">Mendukung GoPay, OVO, DANA, ShopeePay, BCA Mobile, Livin Mandiri, dll.</p>

                {{-- Visual QRIS Dinamis Card --}}
                <div class="bg-white p-6 rounded-3xl border-2 border-slate-200 shadow-sm mx-auto flex flex-col items-center justify-center relative">
                    <div class="mb-3">
                        <span class="text-sm font-black text-rose-600 tracking-wider">QRIS</span>
                    </div>

                    {{-- Dynamic QR SVG Mockup --}}
                    <div class="w-48 h-48 bg-slate-900 rounded-2xl p-3 flex items-center justify-center text-white">
                        <svg class="w-full h-full text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <rect x="2" y="2" width="8" height="8" rx="1"/>
                            <rect x="4" y="4" width="4" height="4" fill="currentColor"/>
                            <rect x="14" y="2" width="8" height="8" rx="1"/>
                            <rect x="16" y="4" width="4" height="4" fill="currentColor"/>
                            <rect x="2" y="14" width="8" height="8" rx="1"/>
                            <rect x="4" y="16" width="4" height="4" fill="currentColor"/>
                            <rect x="14" y="14" width="3" height="3" fill="currentColor"/>
                            <rect x="19" y="14" width="3" height="3" fill="currentColor"/>
                            <rect x="14" y="19" width="8" height="3" fill="currentColor"/>
                        </svg>
                    </div>

                    <div class="mt-3 text-center">
                        <p class="text-xs font-bold text-slate-800">PT FAST TRAVEL INDONESIA</p>
                        <p class="text-[10px] text-slate-400 font-mono">NMID: ID102026090421</p>
                    </div>
                </div>

                <div class="flex gap-2 justify-center pt-2">
                    <button type="button" onclick="alert('Kode QRIS berhasil diunduh ke perangkat Anda.');"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Unduh Gambar QRIS
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 3: E-WALLET INSTANT --}}
        <div x-show="activeTab === 'ewallet'" class="p-6 space-y-4" style="display: none;">
            <h4 class="font-bold text-slate-900 text-sm">Pilih Dompet Digital Anda:</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-4 rounded-xl border border-slate-200 text-center hover:border-emerald-500 cursor-pointer transition bg-slate-50">
                    <span class="font-extrabold text-blue-600 text-sm">GoPay</span>
                    <p class="text-[10px] text-slate-400 mt-1">Instant App</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 text-center hover:border-emerald-500 cursor-pointer transition bg-slate-50">
                    <span class="font-extrabold text-purple-600 text-sm">OVO</span>
                    <p class="text-[10px] text-slate-400 mt-1">Push Notifikasi</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 text-center hover:border-emerald-500 cursor-pointer transition bg-slate-50">
                    <span class="font-extrabold text-sky-500 text-sm">DANA</span>
                    <p class="text-[10px] text-slate-400 mt-1">Direct Debit</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 text-center hover:border-emerald-500 cursor-pointer transition bg-slate-50">
                    <span class="font-extrabold text-orange-500 text-sm">ShopeePay</span>
                    <p class="text-[10px] text-slate-400 mt-1">Deep Link</p>
                </div>
            </div>
            <p class="text-xs text-slate-500">
                Pilih aplikasi e-wallet di atas. Setelah pembayaran disetujui di aplikasi e-wallet, sistem akan otomatis mendeteksi notifikasi webhook.
            </p>
        </div>

    </div>

    {{-- ===== TRIGGER SIMULASI WEBHOOK PAYMENT GATEWAY ===== --}}
    <div class="bg-white rounded-2xl border-2 border-emerald-200 shadow-sm p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                <svg width="18" height="18" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-slate-900 text-base">Verifikasi Otomatis via Payment Gateway Webhook</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem bekerja dengan mekanisme webhook otomatis. Begitu pembayaran berhasil diterima oleh payment gateway (Midtrans/Xendit/VA Bank), webhook mengirim notifikasi ke sistem untuk langsung mengubah status pesanan dari <strong>Pending</strong> menjadi <strong>Confirmed</strong> serta menerbitkan E-Ticket QR.
                </p>
            </div>
        </div>

        {{-- Action Button Simulasi Langsung --}}
        <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('dashboard.user.booking.simulate-payment', $booking) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 text-white font-bold text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2"
                        style="background:#087f55;"
                        onmouseover="this.style.background='#066b47'"
                        onmouseout="this.style.background='#087f55'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simulasi Pembayaran Berhasil (Trigger Webhook)</span>
                </button>
            </form>

            <a href="{{ route('dashboard.user.user-tiket') }}" class="w-full sm:w-auto text-center px-4 py-3 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                Cek Nanti di Tiket Saya
            </a>
        </div>
    </div>

</div>

<script>
function paymentHandler(config) {
    return {
        remainingSeconds: config.remainingSeconds,
        bookingId: config.bookingId,
        activeTab: config.activeTab,

        init() {
            // Hitung mundur payment timer
            setInterval(() => {
                if (this.remainingSeconds > 0) {
                    this.remainingSeconds--;
                } else {
                    alert('Waktu pembayaran telah habis. Transaksi ini dibatalkan otomatis.');
                    window.location.href = "{{ route('dashboard.user.search') }}";
                }
            }, 1000);
        },

        formatTimer(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        }
    };
}
</script>
@endsection
