@extends('layouts.driver')

@section('title', 'Notifikasi Driver - TravelKu')

@section('content')
<div class="space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Notifikasi</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                Pemberitahuan jadwal baru, penumpang baru masuk, dan laporan darurat dari rekan sesama driver.
            </p>
        </div>

        @if (($unreadNotificationsCount ?? 0) > 0)
            <form method="POST" action="{{ route('dashboard.driver.notifikasi.read-all') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl border border-[#DEDAC9] bg-[#FFFDF8] text-xs font-bold text-[#0C5A3E] hover:bg-[#EEF6F1] transition cursor-pointer">
                    Tandai Semua Sudah Dibaca ({{ $unreadNotificationsCount }})
                </button>
            </form>
        @endif
    </header>

    <section class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
        @forelse ($driverNotifications as $notif)
            @php
                $data = $notif->data ?? [];
                $isUnread = is_null($notif->read_at);
                $isEmergency = ($data['status_type'] ?? '') === 'driver_emergency';
            @endphp
            <div class="p-4 sm:p-5 border-b border-[#ECE8DA] last:border-b-0 {{ $isUnread ? 'bg-[#EEF6F1]/50' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="space-y-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($isEmergency)
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-[10px] font-extrabold uppercase">Darurat Rekan Driver</span>
                            @elseif ($isUnread)
                                <span class="px-2 py-0.5 rounded-full bg-[#DFF0E8] text-[#0C5A3E] text-[10px] font-extrabold uppercase">Baru</span>
                            @endif
                            <h2 class="text-sm font-extrabold text-[#13201B]">{{ $data['title'] ?? 'Pemberitahuan Operasional' }}</h2>
                        </div>
                        <p class="text-sm text-[#5C6862]">{{ $data['message'] ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-[#5C6862] whitespace-nowrap">
                        {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                    </span>
                </div>

                @if (! empty($data['deep_link_url']))
                    <div class="mt-2.5">
                        <a href="{{ $data['deep_link_url'] }}" class="text-xs font-bold text-[#0C7950] hover:underline">
                            {{ $data['action_label'] ?? 'Lihat Detail' }} &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 text-center text-sm text-[#5C6862]">
                Belum ada notifikasi masuk untuk akun driver Anda.
            </div>
        @endforelse
    </section>
</div>
@endsection
