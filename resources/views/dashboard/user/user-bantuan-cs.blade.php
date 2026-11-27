@extends('layouts.dashboard')

@section('page-title', 'Bantuan Customer Service')

@section('content')
<div class="space-y-6">
    @include('dashboard.user.partials.flash')

    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Bantuan Customer Service</h1>
            <p class="text-sm text-slate-600 mt-1">
                Hubungi tim Customer Service TravelKu langsung untuk kendala tiket, penjemputan, barang tertinggal, atau pertanyaan perjalanan.
            </p>
        </div>
    </header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        {{-- Kolom Kiri: Form Mulai Chat Baru + Daftar Percakapan --}}
        <aside class="lg:col-span-4 space-y-5">
            {{-- Form Mulai Chat Baru --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Mulai Percakapan Baru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih pesanan Anda (opsional) agar CS langsung melihat detail perjalanan.</p>
                </div>

                <form method="POST" action="{{ route('dashboard.user.bantuan-cs.store') }}" class="space-y-3.5">
                    @csrf

                    @if ($userBookings->isNotEmpty())
                        <div>
                            <label for="user-cs-booking" class="block text-xs font-bold text-slate-700 mb-1">
                                Terkait Pesanan <span class="font-normal text-slate-500">(opsional)</span>
                            </label>
                            <select id="user-cs-booking"
                                    name="booking_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 focus:border-brand-700 focus:outline-none">
                                <option value="">-- Pertanyaan Umum --</option>
                                @foreach ($userBookings as $b)
                                    @php
                                        $origin = $b->schedule?->route?->origin?->city ?? '-';
                                        $dest = $b->schedule?->route?->destination?->city ?? '-';
                                    @endphp
                                    <option value="{{ $b->id }}" @selected((string) old('booking_id') === (string) $b->id)>
                                        {{ $b->booking_code }} &middot; {{ $origin }} → {{ $dest }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label for="user-cs-subject" class="block text-xs font-bold text-slate-700 mb-1">
                            Topik Singkat
                        </label>
                        <input type="text"
                               id="user-cs-subject"
                               name="subject"
                               value="{{ old('subject') }}"
                               placeholder="Misal: Tanya posisi jemput / minta kirim e-tiket"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-700 focus:outline-none">
                    </div>

                    <div>
                        <label for="user-cs-message" class="block text-xs font-bold text-slate-700 mb-1">
                            Pesan Anda <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="user-cs-message"
                                  name="message"
                                  rows="3"
                                  required
                                  placeholder="Jelaskan kendala atau pertanyaan Anda..."
                                  class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-700 focus:outline-none">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-800 transition cursor-pointer">
                        Kirim ke Customer Service
                    </button>
                </form>
            </section>

            {{-- Riwayat Percakapan User --}}
            <section class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-slate-900">Percakapan Saya</h2>
                    <span class="text-xs font-bold text-slate-500">{{ $conversations->count() }}</span>
                </div>

                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse ($conversations as $conv)
                        @php
                            $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                            $lastMsg = $conv->messages->first();
                        @endphp
                        <a href="{{ route('dashboard.user.bantuan-cs', ['conversation' => $conv->id]) }}"
                           class="block p-4 transition {{ $isSelected ? 'bg-brand-50 border-l-4 border-l-brand-700' : 'hover:bg-slate-50' }}">
                            <div class="flex items-start justify-between gap-2">
                                <strong class="text-xs font-extrabold text-slate-900 truncate">{{ $conv->subject ?: $conv->conversation_code }}</strong>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $conv->status === 'resolved' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $conv->status === 'resolved' ? 'Sudah Diatasi' : 'Sedang Ditangani' }}
                                </span>
                            </div>
                            @if ($conv->booking)
                                <p class="mt-0.5 font-mono text-[11px] font-semibold text-brand-700">{{ $conv->booking->booking_code }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-600 line-clamp-2">{{ $lastMsg?->body ?? 'Menunggu balasan CS...' }}</p>
                        </a>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-500">
                            Belum ada percakapan. Gunakan formulir di atas untuk menghubungi Customer Service.
                        </div>
                    @endforelse
                </div>
            </section>
        </aside>

        {{-- Kolom Kanan: Ruang Chat Aktif --}}
        <section class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
            @if (! $activeConversation)
                <div class="p-12 text-center space-y-2">
                    <p class="text-base font-extrabold text-slate-900">Belum ada percakapan yang dipilih</p>
                    <p class="text-xs text-slate-500">Kirim pesan melalui formulir di sebelah kiri untuk langsung terhubung dengan Customer Service TravelKu.</p>
                </div>
            @else
                <div class="border-b border-slate-200 px-5 py-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">{{ $activeConversation->subject ?: 'Percakapan Customer Service' }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $activeConversation->conversation_code }}
                            @if ($activeConversation->booking)
                                &middot; Booking <strong class="font-mono text-slate-700">{{ $activeConversation->booking->booking_code }}</strong>
                            @endif
                            @if ($activeConversation->assignedAgent)
                                &middot; Ditangani oleh <strong class="text-slate-700">{{ $activeConversation->assignedAgent->name }}</strong>
                            @endif
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $activeConversation->status === 'resolved' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                        {{ $activeConversation->status === 'resolved' ? 'Sudah Diatasi' : 'Sedang Ditangani' }}
                    </span>
                </div>

                {{-- Isi Pesan --}}
                <div class="p-5 space-y-3.5 max-h-[440px] overflow-y-auto bg-slate-50/70">
                    @foreach ($activeConversation->messages as $msg)
                        @if ($msg->message_type === 'escalation')
                            <div class="rounded-xl border border-purple-200 bg-purple-50 px-4 py-3 text-xs text-purple-900">
                                <p class="font-bold">Laporan Anda diteruskan ke tim Admin untuk penanganan lanjutan.</p>
                                <p class="mt-0.5 text-[11px] text-purple-700">Tiket: {{ $msg->meta['ticket_code'] ?? '-' }} &middot; {{ $msg->created_at?->format('H:i') }} WIB</p>
                            </div>
                        @elseif ($msg->message_type === 'e_ticket')
                            <div class="flex justify-start">
                                <div class="max-w-[85%] rounded-2xl border border-brand-700 bg-white p-4 shadow-xs text-xs space-y-2">
                                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2">
                                        <span class="font-extrabold text-brand-900">E-Tiket Resmi TravelKu</span>
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                            {{ $msg->meta['status'] ?? 'Aktif' }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-slate-800">
                                        <p><strong class="text-slate-500">Kode Booking:</strong> <span class="font-mono font-bold">{{ $msg->meta['booking_code'] ?? '-' }}</span></p>
                                        <p><strong class="text-slate-500">No. Tiket:</strong> <span class="font-mono">{{ $msg->meta['ticket_number'] ?? '-' }}</span></p>
                                        <p><strong class="text-slate-500">Rute:</strong> {{ $msg->meta['route'] ?? '-' }}</p>
                                        <p><strong class="text-slate-500">Jadwal:</strong> {{ $msg->meta['departure'] ?? '-' }}</p>
                                        <p><strong class="text-slate-500">Kursi &amp; Armada:</strong> Kursi {{ $msg->meta['seats'] ?? '-' }} &middot; {{ $msg->meta['plate_number'] ?? '-' }}</p>
                                    </div>
                                    <p class="text-[10px] text-slate-400">{{ $msg->created_at?->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        @elseif ($msg->sender_type === 'customer')
                            <div class="flex justify-end">
                                <div class="max-w-[82%] rounded-2xl rounded-br-sm bg-brand-700 px-4 py-2.5 text-sm text-white">
                                    <p class="whitespace-pre-line">{{ $msg->body }}</p>
                                    <p class="mt-1 text-[10px] text-white/80 text-right">Anda &middot; {{ $msg->created_at?->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-[82%] rounded-2xl rounded-bl-sm border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900">
                                    <p class="whitespace-pre-line">{{ $msg->body }}</p>
                                    <p class="mt-1 text-[10px] text-slate-500">Customer Service &middot; {{ $msg->created_at?->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Form Balas atau Status Tertutup --}}
                @if ($activeConversation->status === 'resolved')
                    <div class="border-t border-slate-200 bg-emerald-50/70 px-5 py-4 text-center text-xs font-semibold text-emerald-900">
                        Kendala pada percakapan ini telah selesai diatasi dan percakapan telah ditutup. Silakan mulai percakapan baru di sebelah kiri jika ada keperluan lain.
                    </div>
                @else
                    <form method="POST" action="{{ route('dashboard.user.bantuan-cs.reply', $activeConversation) }}" class="border-t border-slate-200 p-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                        @csrf
                        <div class="flex-1">
                            <label for="user-reply-input" class="sr-only">Tulis balasan</label>
                            <textarea id="user-reply-input"
                                      name="message"
                                      rows="2"
                                      required
                                      placeholder="Tulis balasan Anda untuk Customer Service..."
                                      class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-700 focus:outline-none"></textarea>
                        </div>
                        <button type="submit" class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-800 transition shrink-0 cursor-pointer">
                            Kirim
                        </button>
                    </form>
                @endif
            @endif
        </section>
    </div>
</div>
@endsection
