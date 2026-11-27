@extends('layouts.customer-service')

@section('title', 'Live Chat & SLA - Customer Service TravelKu')

@php
    $cityOf = fn ($location) => $location?->city ?: $location?->name ?: '-';
@endphp

@section('content')
<div class="space-y-5 text-[#13201B]">
    {{-- Header --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Live Chat &amp; SLA</h1>
            <p class="text-sm text-[#5C6862] mt-0.5">
                Antrean pesan diurutkan berdasarkan urgensi keberangkatan, dilengkapi konteks booking otomatis, template balasan cepat, kirim e-tiket 1-klik, dan eskalasi Admin.
            </p>
        </div>

        <button type="button"
                onclick="toggleNewChatModal(true)"
                class="inline-flex w-fit items-center gap-2 rounded-xl bg-[#0C7950] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
            + Catat Pesan Masuk
        </button>
    </header>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs text-rose-800 space-y-1">
            <p class="font-bold">Periksa kembali input:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 3-Pane Live Chat Workspace --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 items-start">
        {{-- KOLOM 1: ANTREAN PESAN & SLA TIMER --}}
        <aside class="lg:col-span-3 rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] overflow-hidden shadow-xs">
            <div class="p-4 border-b border-[#ECE8DA] space-y-3">
                <form method="GET" action="{{ route('dashboard.customer-service.live-chat') }}" class="flex gap-1.5">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="search"
                           name="q"
                           value="{{ $search }}"
                           placeholder="Cari nama / kode booking..."
                           class="w-full min-w-0 rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    <button type="submit" class="rounded-xl bg-[#0C392F] px-3 py-2 text-xs font-bold text-white shrink-0">
                        Cari
                    </button>
                </form>

                {{-- Tabs --}}
                <div class="grid grid-cols-2 gap-1.5 text-[11px] font-bold">
                    <a href="{{ route('dashboard.customer-service.live-chat', array_filter(['tab' => 'all', 'q' => $search])) }}"
                       class="rounded-xl border px-2.5 py-2 text-center transition {{ $tab === 'all' ? 'border-[#0C7950] bg-[#DFF0E8] text-[#0C392F]' : 'border-transparent bg-[#F6F4EC] text-[#4B5D55] hover:border-[#B8DFC9] hover:bg-[#EEF6F1] hover:text-[#0C392F]' }}">
                        Aktif ({{ $tabCounts['all'] }})
                    </a>
                    <a href="{{ route('dashboard.customer-service.live-chat', array_filter(['tab' => 'unassigned', 'q' => $search])) }}"
                       class="rounded-xl border px-2.5 py-2 text-center transition {{ $tab === 'unassigned' ? 'border-[#0C7950] bg-[#DFF0E8] text-[#0C392F]' : 'border-transparent bg-[#F6F4EC] text-[#4B5D55] hover:border-[#B8DFC9] hover:bg-[#EEF6F1] hover:text-[#0C392F]' }}">
                        Belum Diambil ({{ $tabCounts['unassigned'] }})
                    </a>
                    <a href="{{ route('dashboard.customer-service.live-chat', array_filter(['tab' => 'assigned', 'q' => $search])) }}"
                       class="rounded-xl border px-2.5 py-2 text-center transition {{ $tab === 'assigned' ? 'border-[#0C7950] bg-[#DFF0E8] text-[#0C392F]' : 'border-transparent bg-[#F6F4EC] text-[#4B5D55] hover:border-[#B8DFC9] hover:bg-[#EEF6F1] hover:text-[#0C392F]' }}">
                        Ditangani Saya ({{ $tabCounts['assigned'] }})
                    </a>
                    <a href="{{ route('dashboard.customer-service.live-chat', array_filter(['tab' => 'resolved', 'q' => $search])) }}"
                       class="rounded-xl border px-2.5 py-2 text-center transition {{ $tab === 'resolved' ? 'border-[#0C7950] bg-[#DFF0E8] text-[#0C392F]' : 'border-transparent bg-[#F6F4EC] text-[#4B5D55] hover:border-[#B8DFC9] hover:bg-[#EEF6F1] hover:text-[#0C392F]' }}">
                        Selesai ({{ $tabCounts['resolved'] }})
                    </a>
                </div>
            </div>

            {{-- Daftar Antrean --}}
            <div class="divide-y divide-[#ECE8DA] max-h-[640px] overflow-y-auto">
                @forelse ($conversations as $conv)
                    @php
                        $urgency = $conv->departureUrgency();
                        $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                        $lastMsg = $conv->messages->first();
                        $slaIso = $conv->slaAnchorIso();
                    @endphp
                    <a href="{{ route('dashboard.customer-service.live-chat', array_filter(['tab' => $tab, 'q' => $search, 'conversation' => $conv->id])) }}"
                       class="block p-3.5 transition {{ $isSelected ? 'bg-[#EEF6F1] border-l-4 border-l-[#0C7950]' : 'hover:bg-[#F6F4EC]/70' }}">
                        <div class="flex items-start justify-between gap-2">
                            <strong class="text-xs font-extrabold text-[#13201B] truncate">{{ $conv->customer_name }}</strong>
                            <span class="shrink-0 inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $urgency['badge_class'] }}">
                                {{ $urgency['label'] }}
                            </span>
                        </div>

                        <div class="mt-1 flex items-center justify-between gap-2 text-[11px] text-[#5C6862]">
                            <span class="font-mono font-semibold truncate">
                                {{ $conv->booking?->booking_code ?? $conv->conversation_code }}
                            </span>
                            @if ($slaIso)
                                <span class="js-sla-timer shrink-0 rounded-md border border-amber-300 bg-amber-50 px-1.5 py-0.5 font-mono text-[10px] font-bold text-amber-900"
                                      data-sla-anchor="{{ $slaIso }}">
                                    SLA ...
                                </span>
                            @elseif ($conv->status === 'resolved')
                                <span class="shrink-0 text-[10px] font-bold text-[#0C5A3E]">Selesai</span>
                            @else
                                <span class="shrink-0 text-[10px] font-semibold text-[#5C6862]">Terbalas</span>
                            @endif
                        </div>

                        <p class="mt-1.5 text-xs text-[#4B5D55] line-clamp-2">
                            {{ $lastMsg?->body ?? $conv->subject }}
                        </p>
                    </a>
                @empty
                    <div class="p-6 text-center text-xs text-[#5C6862]">
                        <p class="font-bold text-[#0C392F]">Antrean kosong.</p>
                        <p class="mt-1">Klik <strong>+ Catat Pesan Masuk</strong> untuk mulai menangani pesan pelanggan.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- KOLOM 2: RUANG PERCAKAPAN, TEMPLATE CEPAT & KIRIM E-TIKET --}}
        <section class="lg:col-span-6 rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] overflow-hidden shadow-xs flex flex-col">
            @if (! $activeConversation)
                <div class="p-12 text-center space-y-2">
                    <p class="text-base font-extrabold text-[#0C392F]">Pilih percakapan di antrean sebelah kiri</p>
                    <p class="text-xs text-[#5C6862]">Atau catat pesan masuk baru dari pelanggan untuk melihat konteks booking otomatis dan SLA timer.</p>
                </div>
            @else
                @php
                    $activeUrgency = $activeConversation->departureUrgency();
                    $activeSlaIso = $activeConversation->slaAnchorIso();
                @endphp

                {{-- Chat Header --}}
                <div class="border-b border-[#ECE8DA] px-5 py-4 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-extrabold text-[#0C392F]">{{ $activeConversation->customer_name }}</h2>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $activeUrgency['badge_class'] }}">
                                {{ $activeUrgency['label'] }}
                            </span>
                            @if ($activeSlaIso)
                                <span class="js-sla-timer inline-flex items-center rounded-full border border-amber-300 bg-amber-50 px-2.5 py-0.5 font-mono text-[11px] font-bold text-amber-900"
                                      data-sla-anchor="{{ $activeSlaIso }}">
                                    Menunggu ...
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[#5C6862] mt-0.5">
                            {{ $activeConversation->conversation_code }}
                            @if ($activeConversation->customer_phone)
                                &middot; {{ $activeConversation->customer_phone }}
                            @endif
                            @if ($activeConversation->assignedAgent)
                                &middot; Agen: <strong class="text-[#13201B]">{{ $activeConversation->assignedAgent->name }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($activeConversation->status === 'unassigned')
                            <form method="POST" action="{{ route('dashboard.customer-service.live-chat.status', $activeConversation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="assign">
                                <button type="submit" class="rounded-lg bg-[#0C7950] px-3 py-1.5 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                                    Ambil Percakapan
                                </button>
                            </form>
                        @endif

                        @if ($activeConversation->status !== 'resolved')
                            <form method="POST" action="{{ route('dashboard.customer-service.live-chat.status', $activeConversation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="resolve">
                                <button type="submit" class="rounded-lg border border-[#DEDAC9] bg-[#F6F4EC] px-3 py-1.5 text-xs font-bold text-[#0C392F] hover:bg-[#ECE8DA] transition">
                                    Tandai Selesai
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('dashboard.customer-service.live-chat.status', $activeConversation) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="reopen">
                                <button type="submit" class="rounded-lg border border-[#DEDAC9] bg-[#F6F4EC] px-3 py-1.5 text-xs font-bold text-[#0C392F] hover:bg-[#ECE8DA] transition">
                                    Buka Kembali
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Daftar Pesan --}}
                <div id="cs-chat-thread" class="p-5 space-y-3.5 max-h-[420px] overflow-y-auto bg-[#F6F4EC]/50">
                    @foreach ($activeConversation->messages as $msg)
                        @if ($msg->message_type === 'escalation')
                            <div class="rounded-xl border border-purple-200 bg-purple-50 px-4 py-3 text-xs text-purple-900">
                                <p class="font-extrabold">Eskalasi ke Admin &middot; {{ $msg->meta['ticket_code'] ?? '' }}</p>
                                <p class="mt-1">{{ $msg->body }}</p>
                                <p class="mt-1 text-[10px] text-purple-700">{{ $msg->created_at?->format('H:i') }} WIB</p>
                            </div>
                        @elseif ($msg->message_type === 'internal_note')
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-950">
                                <p class="font-extrabold">Catatan Internal CS ({{ $msg->sender?->name ?? 'CS' }})</p>
                                <p class="mt-1 whitespace-pre-line">{{ $msg->body }}</p>
                                <p class="mt-1 text-[10px] text-amber-700">{{ $msg->created_at?->format('H:i') }} WIB</p>
                            </div>
                        @elseif ($msg->message_type === 'e_ticket')
                            <div class="flex justify-end">
                                <div class="max-w-[85%] rounded-2xl border border-[#0C7950] bg-white p-4 shadow-xs text-xs space-y-2">
                                    <div class="flex items-center justify-between gap-3 border-b border-[#ECE8DA] pb-2">
                                        <span class="font-extrabold text-[#0C392F]">E-Tiket Resmi TravelKu</span>
                                        <span class="rounded-full bg-[#DFF0E8] px-2 py-0.5 text-[10px] font-bold text-[#0C5A3E]">
                                            {{ $msg->meta['status'] ?? 'Aktif' }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-[#13201B]">
                                        <p><strong class="text-[#5C6862]">Kode Booking:</strong> <span class="font-mono font-bold">{{ $msg->meta['booking_code'] ?? '-' }}</span></p>
                                        <p><strong class="text-[#5C6862]">No. Tiket:</strong> <span class="font-mono">{{ $msg->meta['ticket_number'] ?? '-' }}</span></p>
                                        <p><strong class="text-[#5C6862]">Rute:</strong> {{ $msg->meta['route'] ?? '-' }}</p>
                                        <p><strong class="text-[#5C6862]">Jadwal:</strong> {{ $msg->meta['departure'] ?? '-' }}</p>
                                        <p><strong class="text-[#5C6862]">Kursi &amp; Armada:</strong> Kursi {{ $msg->meta['seats'] ?? '-' }} &middot; {{ $msg->meta['plate_number'] ?? '-' }}</p>
                                    </div>
                                    <p class="text-[10px] text-[#5C6862] text-right">Dikirim {{ $msg->created_at?->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        @elseif ($msg->sender_type === 'agent')
                            <div class="flex justify-end">
                                <div class="max-w-[82%] rounded-2xl rounded-br-sm bg-[#0C7950] px-4 py-2.5 text-sm text-white">
                                    <p class="whitespace-pre-line">{{ $msg->body }}</p>
                                    <p class="mt-1 text-[10px] text-white/80 text-right">
                                        {{ $msg->sender?->name ?? 'CS' }} &middot; {{ $msg->created_at?->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="max-w-[82%] rounded-2xl rounded-bl-sm border border-[#DEDAC9] bg-white px-4 py-2.5 text-sm text-[#13201B]">
                                    <p class="whitespace-pre-line">{{ $msg->body }}</p>
                                    <p class="mt-1 text-[10px] text-[#5C6862]">
                                        {{ $activeConversation->customer_name }} &middot; {{ $msg->created_at?->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Template Balasan Cepat + Kirim E-Tiket 1-Klik --}}
                <div class="border-t border-[#ECE8DA] bg-white px-5 py-3 space-y-2.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#5C6862]">Template Balasan Cepat</span>
                        @if ($activeConversation->booking)
                            <form method="POST" action="{{ route('dashboard.customer-service.live-chat.send-eticket', $activeConversation) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-[#0C7950] bg-[#EEF6F1] px-3 py-1 text-xs font-bold text-[#0C5A3E] hover:bg-[#DFF0E8] transition">
                                    Kirim E-Tiket (1-Klik)
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($quickTemplates as $tpl)
                            <button type="button"
                                    onclick="applyQuickReply(@js($tpl['text']))"
                                    class="rounded-full border border-[#DEDAC9] bg-[#F6F4EC] px-3 py-1 text-[11px] font-semibold text-[#13201B] hover:border-[#0C7950] hover:bg-[#EEF6F1] transition">
                                {{ $tpl['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Composer Balasan --}}
                <form method="POST" action="{{ route('dashboard.customer-service.live-chat.reply', $activeConversation) }}" class="border-t border-[#ECE8DA] bg-[#FFFDF8] p-4 space-y-3">
                    @csrf
                    <textarea id="cs-reply-input"
                              name="body"
                              rows="3"
                              required
                              placeholder="Tulis balasan untuk pelanggan atau klik template cepat di atas..."
                              class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm text-[#13201B] focus:border-[#0C7950] focus:outline-none focus:ring-2 focus:ring-[#0C7950]/20"></textarea>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#5C6862] cursor-pointer">
                            <input type="checkbox" name="is_internal_note" value="1" class="rounded border-[#DEDAC9] text-[#0C7950]">
                            <span>Simpan sebagai Catatan Internal (tidak dikirim ke pelanggan)</span>
                        </label>

                        <button type="submit" class="rounded-xl bg-[#0C7950] px-5 py-2.5 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            @endif
        </section>

        {{-- KOLOM 3: KONTEKS BOOKING OTOMATIS & ESKALASI KE ADMIN --}}
        <aside class="lg:col-span-3 space-y-5">
            {{-- Konteks Booking Otomatis --}}
            <div class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-extrabold text-[#0C392F]">Konteks Booking Otomatis</h2>
                    <span class="rounded-md bg-[#F6F4EC] px-2 py-0.5 text-[10px] font-bold text-[#5C6862]">Read-Only</span>
                </div>

                @if ($activeConversation?->booking)
                    @php
                        $bk = $activeConversation->booking;
                        $sched = $bk->schedule;
                        $origin = $cityOf($sched?->route?->origin);
                        $dest = $cityOf($sched?->route?->destination);
                        $seats = $bk->passengerSeatNumbers() ?: $bk->total_passenger.' kursi';
                    @endphp
                    <div class="space-y-3 text-xs">
                        <div class="rounded-xl bg-[#F6F4EC]/80 p-3 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-sm font-extrabold text-[#0C392F]">{{ $bk->booking_code }}</span>
                                <span class="rounded-full bg-[#DFF0E8] px-2 py-0.5 text-[10px] font-bold text-[#0C5A3E]">
                                    {{ $bk->statusLabel() }}
                                </span>
                            </div>
                            @if ($bk->ticket)
                                <p class="text-[#5C6862]">Tiket: <span class="font-mono font-semibold">{{ $bk->ticket->ticket_number }}</span></p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#5C6862]">Rute &amp; Jadwal</p>
                                <p class="font-bold text-[#13201B]">{{ $origin }} &rarr; {{ $dest }}</p>
                                @if ($sched)
                                    <p class="text-[#4B5D55]">{{ $sched->departureCarbon()->translatedFormat('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>

                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#5C6862]">Kursi &amp; Penumpang</p>
                                <p class="font-bold text-[#0C392F]">Kursi {{ $seats }}</p>
                                <p class="text-[#4B5D55]">{{ $bk->passengers->pluck('name')->filter()->implode(', ') ?: $activeConversation->customer_name }}</p>
                            </div>

                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#5C6862]">Armada &amp; Kontak Sopir</p>
                                <p class="font-bold text-[#13201B]">
                                    {{ $sched?->vehicle?->plate_number ?? 'Belum ditugaskan' }}
                                    @if ($sched?->vehicle?->name)
                                        <span class="font-normal text-[#5C6862]">({{ $sched->vehicle->name }})</span>
                                    @endif
                                </p>
                                <p class="text-[#4B5D55] mt-0.5">Sopir: {{ $driverContact?->name ?? 'Belum ditugaskan' }}</p>

                                @if ($driverContact && ($driverContact->wa_url || $driverContact->tel_phone !== ''))
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if ($driverContact->wa_url)
                                            <a href="{{ $driverContact->wa_url }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="inline-flex items-center rounded-lg bg-[#0C7950] px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-[#0C5A3E] transition">
                                                WhatsApp Sopir
                                            </a>
                                        @endif
                                        @if ($driverContact->tel_phone !== '')
                                            <a href="tel:{{ $driverContact->tel_phone }}"
                                               class="inline-flex items-center rounded-lg border border-[#DEDAC9] bg-white px-2.5 py-1.5 text-[11px] font-bold text-[#13201B] hover:bg-[#F6F4EC] transition">
                                                Telepon
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <p class="rounded-xl border border-dashed border-[#DEDAC9] bg-[#F6F4EC]/60 p-4 text-xs text-[#5C6862]">
                        Percakapan ini belum terhubung ke Kode Booking. Cantumkan kode booking saat mencatat pesan masuk agar rute, kursi, dan kontak sopir muncul otomatis.
                    </p>
                @endif
            </div>

            {{-- Eskalasi ke Admin --}}
            @if ($activeConversation)
                <div class="rounded-2xl border border-purple-200 bg-[#FFFDF8] p-5 shadow-xs space-y-3">
                    <div>
                        <h2 class="text-sm font-extrabold text-purple-900">Eskalasi ke Admin</h2>
                        <p class="text-[11px] text-[#5C6862] mt-0.5">Alihkan kasus refund, barang hilang, atau armada mogok ke Admin.</p>
                    </div>

                    <form method="POST" action="{{ route('dashboard.customer-service.live-chat.escalate', $activeConversation) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="chat-escalation-type" class="block text-[11px] font-bold text-[#0C392F] mb-1">Jenis Kasus</label>
                            <select id="chat-escalation-type"
                                    name="escalation_type"
                                    required
                                    class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-semibold text-[#13201B]">
                                <option value="refund">Pengajuan Refund / Keuangan</option>
                                <option value="barang_hilang">Barang Hilang / Tertinggal</option>
                                <option value="armada_mogok">Armada Mogok / Ganti Mobil</option>
                                <option value="lainnya">Kendala Khusus Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label for="chat-escalation-reason" class="block text-[11px] font-bold text-[#0C392F] mb-1">Ringkasan Kasus</label>
                            <textarea id="chat-escalation-reason"
                                      name="escalation_reason"
                                      rows="3"
                                      required
                                      placeholder="Jelaskan kronologi & kebutuhan tindak lanjut Admin..."
                                      class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs text-[#13201B] focus:border-purple-500 focus:outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-purple-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-purple-800 transition">
                            Eskalasi ke Admin
                        </button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('modals')
{{-- Modal Catat Pesan Masuk Baru --}}
<div id="new-chat-modal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-[#0C392F]">Catat Pesan Masuk Pelanggan</h3>
            <button type="button" onclick="toggleNewChatModal(false)" class="text-lg font-bold text-[#5C6862]">&times;</button>
        </div>

        <form method="POST" action="{{ route('dashboard.customer-service.live-chat.store') }}" class="space-y-3.5">
            @csrf
            <div>
                <label for="new-chat-booking-code" class="block text-xs font-bold text-[#0C392F] mb-1">
                    Kode Booking <span class="font-normal text-[#5C6862]">(opsional — otomatis muat rute &amp; sopir)</span>
                </label>
                <input type="text"
                       id="new-chat-booking-code"
                       name="booking_code"
                       placeholder="Contoh: TRV-20261127-001"
                       class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-sm font-mono font-semibold text-[#13201B]">
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="new-chat-name" class="block text-xs font-bold text-[#0C392F] mb-1">Nama Pelanggan</label>
                    <input type="text"
                           id="new-chat-name"
                           name="customer_name"
                           placeholder="Otomatis dari booking"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs text-[#13201B]">
                </div>
                <div>
                    <label for="new-chat-phone" class="block text-xs font-bold text-[#0C392F] mb-1">Nomor HP</label>
                    <input type="text"
                           id="new-chat-phone"
                           name="customer_phone"
                           placeholder="08xxxxxxxxxx"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs text-[#13201B]">
                </div>
            </div>

            <div>
                <label for="new-chat-message" class="block text-xs font-bold text-[#0C392F] mb-1">
                    Pesan Pelanggan <span class="text-rose-600">*</span>
                </label>
                <textarea id="new-chat-message"
                          name="initial_message"
                          rows="3"
                          required
                          placeholder="Tuliskan pesan atau pertanyaan awal pelanggan..."
                          class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-sm text-[#13201B]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="toggleNewChatModal(false)" class="rounded-xl border border-[#DEDAC9] bg-white px-4 py-2 text-xs font-bold text-[#5C6862]">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#0C7950] px-4 py-2 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                    Masuk Antrean
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function toggleNewChatModal(show) {
        var modal = document.getElementById('new-chat-modal');
        if (!modal) return;
        modal.classList.toggle('hidden', !show);
    }

    function applyQuickReply(text) {
        var input = document.getElementById('cs-reply-input');
        if (!input) return;
        input.value = text;
        input.focus();
    }

    // Live SLA Timer updater
    (function () {
        function updateTimers() {
            var now = Date.now();
            document.querySelectorAll('.js-sla-timer[data-sla-anchor]').forEach(function (el) {
                var anchor = Date.parse(el.getAttribute('data-sla-anchor'));
                if (isNaN(anchor)) return;
                var diffSec = Math.max(0, Math.floor((now - anchor) / 1000));
                var mins = Math.floor(diffSec / 60);
                var secs = diffSec % 60;
                var formatted = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                el.textContent = 'SLA ' + formatted;

                if (mins >= 15) {
                    el.className = 'js-sla-timer shrink-0 rounded-md border border-rose-300 bg-rose-100 px-2 py-0.5 font-mono text-[10px] font-bold text-rose-800';
                } else if (mins >= 5) {
                    el.className = 'js-sla-timer shrink-0 rounded-md border border-amber-300 bg-amber-50 px-2 py-0.5 font-mono text-[10px] font-bold text-amber-900';
                } else {
                    el.className = 'js-sla-timer shrink-0 rounded-md border border-[#B8DFC9] bg-[#EEF6F1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0C5A3E]';
                }
            });
        }

        updateTimers();
        setInterval(updateTimers, 1000);

        var thread = document.getElementById('cs-chat-thread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    })();
</script>
@endpush
