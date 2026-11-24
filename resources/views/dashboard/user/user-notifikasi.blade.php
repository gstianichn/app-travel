@extends('layouts.dashboard')

@section('page-title', 'Notifikasi')

@php
    $categories = [
        null => ['Semua', null],
        'transaksi' => ['Status Transaksi', $countTransaksi],
        'pengingat' => ['Pengingat (H-2 Jam)', $countPengingat],
        'operasional' => ['Info Operasional', $countOperasional],
    ];
    $testTriggers = [
        'pending' => 'Menunggu bayar',
        'confirmed' => 'Pembayaran berhasil',
        'refund_submitted' => 'Refund diajukan',
        'refund_approved' => 'Refund disetujui',
        'refund_rejected' => 'Refund ditolak',
        'expired' => 'Waktu bayar habis',
        'reminder_2h' => 'Pengingat 2 jam',
        'operational_vehicle' => 'Pelat dan titik jemput',
        'operational_delay' => 'Terlambat 20 menit',
    ];
@endphp

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="notifikasiApp()">

    @include('dashboard.user.partials.flash')

    {{-- HEADER --}}
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-brand-900">Pemberitahuan</h1>
            <p class="mt-1 text-sm text-slate-600">
                @if ($unreadCount > 0)
                    <strong class="text-brand-800">{{ $unreadCount }} belum dibaca.</strong>
                @else
                    Semua sudah dibaca.
                @endif
                Kabar pembayaran, status refund, pengingat 2 jam sebelum berangkat, dan info armada di hari&#8209;H.
            </p>
        </div>
        @if ($unreadCount > 0)
            <form action="{{ route('dashboard.user.notifikasi.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center min-h-10 px-4 rounded-lg border border-brand-700 bg-white text-sm font-semibold text-brand-800 hover:bg-brand-50 transition-colors cursor-pointer">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </header>

    {{-- IZIN NOTIFIKASI BROWSER --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3.5" x-show="pushPermission !== 'unsupported'">
        <p class="text-sm text-slate-700" x-show="pushPermission !== 'granted'">Mau kabar pembayaran dan keberangkatan muncul juga di layar HP atau laptop ini?</p>
        <p class="text-sm font-semibold text-brand-800" x-show="pushPermission === 'granted'" x-cloak>Notifikasi browser aktif di perangkat ini.</p>
        <button type="button" @click="requestPushPermission()" x-show="pushPermission === 'default'"
                class="inline-flex items-center min-h-9 px-4 rounded-lg bg-brand-800 text-xs font-bold text-white hover:bg-brand-900 transition-colors cursor-pointer">
            Izinkan notifikasi
        </button>
        <p class="text-xs text-slate-500" x-show="pushPermission === 'denied'" x-cloak>Notifikasi diblokir. Buka pengaturan situs di browser untuk mengizinkannya.</p>
    </div>

    {{-- TAB KATEGORI --}}
    <nav class="flex flex-wrap gap-1 border-b border-slate-200" aria-label="Saring pemberitahuan">
        @foreach ($categories as $key => [$label, $count])
            @php $isCurrent = (string) $selectedCategory === (string) $key; @endphp
            <a href="{{ route('dashboard.user.user-notifikasi', array_filter(['category' => $key])) }}"
               @if ($isCurrent) aria-current="page" @endif
               @class([
                   '-mb-px inline-flex shrink-0 items-center gap-2 min-h-11 px-4 border-b-2 text-sm font-bold transition-colors',
                   'border-brand-800 text-brand-900' => $isCurrent,
                   'border-transparent text-slate-500 hover:text-brand-800' => ! $isCurrent,
               ])>
                <span>{{ $label }}</span>
                @if ($count)
                    <span class="text-xs font-semibold text-slate-400">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- DAFTAR PEMBERITAHUAN --}}
    @if ($notifications->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <p class="font-bold text-slate-900">Belum ada pemberitahuan{{ $selectedCategory ? ' di kelompok ini' : '' }}.</p>
            <p class="mt-1 text-sm text-slate-600">Kabar pertama datang otomatis setelah kamu memesan tiket atau mengajukan refund.</p>
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <ul class="divide-y divide-slate-100">
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $meta = $data['meta'] ?? [];
                        $isUnread = $notification->unread();
                        $statusType = strtolower((string) ($data['status_type'] ?? ''));
                        $cat = strtolower((string) ($data['category'] ?? ''));

                        [$badgeText, $badgeClass, $dotClass] = match (true) {
                            in_array($statusType, ['confirmed', 'paid'], true) => ['Pembayaran Berhasil', 'bg-brand-100 text-brand-900', 'bg-brand-700'],
                            $statusType === 'pending' => ['Menunggu Pembayaran', 'bg-amber-100 text-amber-900', 'bg-amber-600'],
                            $statusType === 'refund_approved' => ['Refund Disetujui', 'bg-emerald-100 text-emerald-900', 'bg-emerald-600'],
                            $statusType === 'refund_rejected' => ['Refund Ditolak', 'bg-rose-50 text-rose-800', 'bg-rose-600'],
                            $statusType === 'refund_submitted' => ['Refund Diajukan', 'bg-sky-50 text-sky-900', 'bg-sky-600'],
                            in_array($statusType, ['expired', 'cancelled', 'admin_cancelled'], true) => ['Dibatalkan', 'bg-rose-50 text-rose-800', 'bg-rose-600'],
                            $cat === 'pengingat' => ['Pengingat H-2 Jam', 'bg-amber-50 text-amber-900', 'bg-amber-600'],
                            $cat === 'operasional' => ['Info Hari-H', 'bg-slate-100 text-slate-700', 'bg-slate-600'],
                            default => ['Status Transaksi', 'bg-slate-100 text-slate-700', 'bg-brand-700'],
                        };

                        $details = array_filter([
                            ! empty($meta['booking_code']) ? $meta['booking_code'] : null,
                            $meta['route'] ?? null,
                            ! empty($meta['seats']) ? 'Kursi '.$meta['seats'] : null,
                            ! empty($meta['pool_arrival_time']) ? 'Tiba di pool maks '.$meta['pool_arrival_time'] : null,
                            ! empty($meta['plate_number']) ? 'Pelat '.$meta['plate_number'] : null,
                            $meta['pickup_location'] ?? null,
                            ! empty($meta['delay_minutes']) ? 'Mundur '.$meta['delay_minutes'].' menit' : null,
                        ]);
                    @endphp
                    <li @class([
                        'px-5 sm:px-6 py-4 sm:py-5 transition-colors hover:bg-slate-50/70',
                        'bg-brand-50/35' => $isUnread,
                    ])>
                        <div class="flex items-start gap-3.5">
                            <span @class([
                                'mt-2 h-2.5 w-2.5 shrink-0 rounded-full',
                                $dotClass => $isUnread,
                                'bg-slate-200' => ! $isUnread,
                            ]) aria-hidden="true"></span>

                            <div class="min-w-0 flex-1 space-y-1.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>
                                        @if ($isUnread)
                                            <span class="text-xs font-bold text-brand-800">Baru</span>
                                        @endif
                                    </div>
                                    <time class="text-xs text-slate-500" datetime="{{ $notification->created_at->toIso8601String() }}">
                                        {{ $notification->created_at->locale('id')->diffForHumans() }}
                                    </time>
                                </div>

                                <h2 @class(['text-sm sm:text-base text-slate-900 leading-snug', 'font-extrabold' => $isUnread, 'font-bold' => ! $isUnread])>
                                    <a href="{{ route('dashboard.user.notifikasi.open', $notification->id) }}" class="hover:text-brand-800 hover:underline">
                                        {{ $data['title'] ?? 'Pemberitahuan' }}
                                    </a>
                                </h2>

                                <p class="text-sm text-slate-600 leading-relaxed">{{ $data['message'] ?? '' }}</p>

                                @if ($details)
                                    <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                        @foreach ($details as $detailItem)
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $detailItem }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="pt-2 flex flex-wrap items-center justify-between gap-3">
                                    <a href="{{ route('dashboard.user.notifikasi.open', $notification->id) }}"
                                       class="inline-flex items-center gap-1 text-sm font-bold text-brand-800 hover:text-brand-950 underline underline-offset-4">
                                        <span>{{ $data['action_label'] ?? 'Buka' }}</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>

                                    <form action="{{ route('dashboard.user.notifikasi.destroy', $notification->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-700 transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif

    @unless (app()->isProduction())
        {{-- Developer tool untuk mencoba tiap tipe notifikasi; tidak tampil di production --}}
        <details class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-3.5 text-slate-700">
            <summary class="cursor-pointer text-sm font-semibold">Mode uji: kirim contoh pemberitahuan (tidak tampil di production)</summary>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($testTriggers as $type => $label)
                    <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <button type="submit" class="inline-flex items-center min-h-9 px-3 rounded-lg border border-slate-300 bg-slate-50 text-xs font-semibold hover:border-brand-700 hover:bg-white cursor-pointer">{{ $label }}</button>
                    </form>
                @endforeach
            </div>
        </details>
    @endunless
</div>

<script>
function notifikasiApp() {
    return {
        pushPermission: ('Notification' in window) ? Notification.permission : 'unsupported',

        init() {
            @if (session()->has('queued_push_notifications'))
                const queued = @json(session('queued_push_notifications'));
                (Array.isArray(queued) ? queued : []).forEach((item) => this.showDeviceNotification(item));
            @endif
        },

        requestPushPermission() {
            Notification.requestPermission().then((permission) => {
                this.pushPermission = permission;
            });
        },

        showDeviceNotification(item) {
            if (!('Notification' in window) || Notification.permission !== 'granted') {
                return;
            }

            const notification = new Notification(item.title, { body: item.body, icon: '/favicon.ico' });

            notification.onclick = (event) => {
                event.preventDefault();
                window.focus();

                if (item.deep_link_url) {
                    window.location.href = item.deep_link_url;
                }
            };
        },
    };
}
</script>
@endsection
