@extends('layouts.dashboard')

@section('page-title', 'Notifikasi')

@php
    $categories = [
        null => ['Semua', null],
        'transaksi' => ['Status Transaksi', $countTransaksi],
        'pengingat' => ['Pengingat (H-2 Jam)', $countPengingat],
        'operasional' => ['Info Operasional', $countOperasional],
    ];
    $categoryLabels = ['transaksi' => 'Pembayaran', 'pengingat' => 'Pengingat berangkat', 'operasional' => 'Info hari-H'];
    $testTriggers = [
        'pending' => 'Menunggu bayar',
        'confirmed' => 'Pembayaran masuk',
        'expired' => 'Waktu bayar habis',
        'reminder_2h' => 'Pengingat 2 jam',
        'operational_vehicle' => 'Pelat dan titik jemput',
        'operational_delay' => 'Terlambat 20 menit',
    ];
@endphp

@section('content')
<div class="space-y-8 max-w-3xl" x-data="notifikasiApp()">

    @include('dashboard.user.partials.flash')

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-brand-900">Pemberitahuan</h1>
            <p class="mt-1 text-slate-600">
                @if ($unreadCount > 0)
                    {{ $unreadCount }} belum dibaca.
                @else
                    Semua sudah dibaca.
                @endif
                Kabar pembayaran, pengingat 2 jam sebelum berangkat, dan info kendaraan di hari&#8209;H.
            </p>
        </div>
        @if ($unreadCount > 0)
            <form action="{{ route('dashboard.user.notifikasi.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center min-h-11 px-4 rounded-lg border-2 border-brand-800 font-bold text-brand-800 hover:bg-brand-50">Tandai semua dibaca</button>
            </form>
        @endif
    </header>

    {{-- Browser notifications are optional; the history below works without them --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-300 bg-white px-5 py-4" x-show="pushPermission !== 'unsupported'">
        <p class="text-slate-700" x-show="pushPermission !== 'granted'">Mau kabar ini muncul juga di layar HP atau laptop ini?</p>
        <p class="font-semibold text-brand-800" x-show="pushPermission === 'granted'" x-cloak>Notifikasi browser aktif di perangkat ini.</p>
        <button type="button" @click="requestPushPermission()" x-show="pushPermission === 'default'"
                class="inline-flex items-center min-h-11 px-4 rounded-lg bg-brand-800 font-bold text-white hover:bg-brand-900">Izinkan notifikasi</button>
        <p class="text-sm text-slate-600" x-show="pushPermission === 'denied'" x-cloak>Notifikasi diblokir. Buka pengaturan situs di browser untuk mengizinkannya.</p>
    </div>

    <nav class="flex gap-1 overflow-x-auto border-b border-slate-300" aria-label="Saring pemberitahuan">
        @foreach ($categories as $key => [$label, $count])
            @php $isCurrent = (string) $selectedCategory === (string) $key; @endphp
            <a href="{{ route('dashboard.user.user-notifikasi', array_filter(['category' => $key])) }}"
               @if ($isCurrent) aria-current="page" @endif
               @class([
                   '-mb-px inline-flex shrink-0 items-center gap-2 min-h-11 px-4 border-b-[3px] font-bold',
                   'border-brand-800 text-brand-900' => $isCurrent,
                   'border-transparent text-slate-600 hover:text-brand-800' => ! $isCurrent,
               ])>
                {{ $label }}
                @if ($count)
                    <span class="text-sm font-semibold text-slate-500">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    @if ($notifications->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-8">
            <p class="font-bold text-slate-900">Belum ada pemberitahuan{{ $selectedCategory ? ' di kelompok ini' : '' }}.</p>
            <p class="mt-1 text-slate-600">Kabar pertama datang setelah kamu memesan tiket.</p>
        </div>
    @else
        <ul class="divide-y divide-slate-200 border-y border-slate-200">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $meta = $data['meta'] ?? [];
                    $isUnread = $notification->unread();
                    $details = array_filter([
                        $meta['route'] ?? null,
                        ! empty($meta['seats']) ? 'kursi '.$meta['seats'] : null,
                        ! empty($meta['pool_arrival_time']) ? 'tiba di pool paling lambat '.$meta['pool_arrival_time'] : null,
                        ! empty($meta['plate_number']) ? 'pelat '.$meta['plate_number'] : null,
                        $meta['pickup_location'] ?? null,
                        ! empty($meta['delay_minutes']) ? 'mundur '.$meta['delay_minutes'].' menit' : null,
                    ]);
                @endphp
                <li @class(['flex gap-4 py-5', 'bg-brand-50/60 -mx-4 px-4' => $isUnread])>
                    <span @class(['mt-2 w-2.5 h-2.5 shrink-0 rounded-full', 'bg-brand-700' => $isUnread, 'bg-transparent' => ! $isUnread]) aria-hidden="true"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-600">
                            @if ($isUnread)
                                <b class="text-brand-800">Baru.</b>
                            @endif
                            {{ $categoryLabels[$data['category'] ?? ''] ?? 'Pemberitahuan' }},
                            <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->locale('id')->diffForHumans() }}</time>
                        </p>
                        <h2 @class(['mt-1 text-slate-900', 'font-extrabold' => $isUnread, 'font-semibold' => ! $isUnread])>
                            <a href="{{ route('dashboard.user.notifikasi.open', $notification->id) }}" class="hover:text-brand-800 hover:underline">
                                {{ $data['title'] ?? 'Pemberitahuan' }}
                            </a>
                        </h2>
                        <p class="mt-1 text-slate-700">{{ $data['message'] ?? '' }}</p>
                        @if ($details)
                            <p class="mt-1 text-sm text-slate-600">{{ ucfirst(implode(', ', $details)) }}.</p>
                        @endif
                        <div class="mt-2 flex flex-wrap items-center gap-x-5">
                            <a href="{{ route('dashboard.user.notifikasi.open', $notification->id) }}" class="inline-flex items-center min-h-11 font-semibold text-brand-700 underline underline-offset-2">
                                {{ $data['action_label'] ?? 'Buka' }}
                            </a>
                            <form action="{{ route('dashboard.user.notifikasi.destroy', $notification->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center min-h-11 text-sm font-semibold text-slate-600 hover:text-rose-800">Hapus</button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($notifications->hasPages())
            {{ $notifications->links() }}
        @endif
    @endif

    @unless (app()->isProduction())
        {{-- TODO: developer tool for trying each notification type; hidden in production --}}
        <details class="rounded-xl border-2 border-dashed border-slate-400 px-6 py-4 text-slate-700">
            <summary class="cursor-pointer font-semibold">Mode uji: kirim contoh pemberitahuan (tidak tampil di production)</summary>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($testTriggers as $type => $label)
                    <form action="{{ route('dashboard.user.notifikasi.simulate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <button type="submit" class="inline-flex items-center min-h-11 px-3 rounded-lg border border-slate-400 bg-white text-sm font-semibold hover:border-slate-700">{{ $label }}</button>
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
