@extends('layouts.customer-service')

@section('title', 'Laporan & Tiket Kendala - Customer Service TravelKu')

@php
    $complaintStatusBadge = fn (string $status): string => match ($status) {
        'baru_masuk' => 'bg-amber-50 text-amber-800 border-amber-200',
        'sedang_dicek' => 'bg-sky-50 text-sky-800 border-sky-200',
        'selesai' => 'bg-[#DFF0E8] text-[#0C5A3E] border-[#B8DFC9]',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

@section('content')
<div class="space-y-6 text-[#13201B]">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0C392F]">Laporan &amp; Tiket Kendala</h1>
            <p class="text-sm text-[#5C6862] mt-1">
                Catat kendala pelanggan, perbarui status penanganan, simpan catatan tindak lanjut/solusi, atau eskalasi ke Admin.
            </p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full bg-[#EEF6F1] px-3 py-1 text-xs font-bold text-[#0C5A3E]">
            {{ $complaintStats['total'] }} Total Tiket
        </span>
    </header>

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Form Catat Laporan Kendala Baru --}}
        <div id="form-kendala" class="lg:col-span-4 rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs h-fit space-y-4">
            <div>
                <h2 class="text-base font-extrabold text-[#0C392F]">Catat Laporan Kendala Baru</h2>
                <p class="text-xs text-[#5C6862] mt-0.5">
                    Kode booking terisi otomatis bila Anda berasal dari halaman Cek Tiket.
                </p>
            </div>

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800 space-y-1">
                    <p class="font-bold">Periksa kembali isian formulir:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.customer-service.complaints.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="complaint-booking-code" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Kode Booking <span class="font-normal text-[#5C6862]">(opsional / otomatis)</span>
                    </label>
                    <input type="text"
                           id="complaint-booking-code"
                           name="booking_code"
                           value="{{ old('booking_code', $prefilledBookingCode) }}"
                           placeholder="Contoh: TRV-20261127-001"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-mono font-semibold text-[#13201B] focus:border-[#0C7950] focus:outline-none focus:ring-2 focus:ring-[#0C7950]/20">
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="complaint-customer-name" class="block text-xs font-bold text-[#0C392F] mb-1">
                            Nama Pelanggan
                        </label>
                        <input type="text"
                               id="complaint-customer-name"
                               name="customer_name"
                               value="{{ old('customer_name', $prefilledCustomerName) }}"
                               placeholder="Otomatis dari booking"
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    </div>
                    <div>
                        <label for="complaint-customer-phone" class="block text-xs font-bold text-[#0C392F] mb-1">
                            No. HP Pelanggan
                        </label>
                        <input type="text"
                               id="complaint-customer-phone"
                               name="customer_phone"
                               value="{{ old('customer_phone', $prefilledCustomerPhone) }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="complaint-category" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Kategori Kendala <span class="text-rose-600">*</span>
                    </label>
                    <select id="complaint-category"
                            name="category"
                            required
                            class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-semibold text-[#13201B] focus:border-[#0C7950] focus:outline-none focus:ring-2 focus:ring-[#0C7950]/20">
                        <option value="">-- Pilih Kategori Kendala --</option>
                        @foreach ($complaintCategories as $catKey => $catLabel)
                            <option value="{{ $catKey }}" @selected(old('category') === $catKey)>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="complaint-description" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Deskripsi / Kronologi Kendala <span class="text-rose-600">*</span>
                    </label>
                    <textarea id="complaint-description"
                              name="description"
                              rows="4"
                              required
                              placeholder="Tuliskan kronologi kendala yang disampaikan pelanggan secara jelas..."
                              class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm text-[#13201B] focus:border-[#0C7950] focus:outline-none focus:ring-2 focus:ring-[#0C7950]/20">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full rounded-xl bg-[#0C7950] px-4 py-3 text-sm font-bold text-white hover:bg-[#0C5A3E] transition">
                    Simpan Laporan Kendala
                </button>
            </form>
        </div>

        {{-- Daftar Tiket Laporan Kendala --}}
        <div class="lg:col-span-8 rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs space-y-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-[#0C392F]">Daftar Tiket Laporan Masuk</h2>
                    <p class="text-xs text-[#5C6862]">Filter, ubah status penanganan, catat solusi, atau eskalasi ke Admin.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-[#F6F4EC] border border-[#DEDAC9] px-3 py-1 text-xs font-bold text-[#0C392F]">
                    {{ $complaints->count() }} Ditampilkan
                </span>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('dashboard.customer-service.laporan-kendala') }}" class="grid grid-cols-1 gap-2.5 sm:grid-cols-12">
                <div class="sm:col-span-4">
                    <select name="complaint_status"
                            aria-label="Filter Status Kendala"
                            class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-semibold text-[#13201B]">
                        <option value="all" @selected($complaintStatus === 'all')>Semua Status ({{ $complaintStats['total'] }})</option>
                        <option value="baru_masuk" @selected($complaintStatus === 'baru_masuk')>Baru Masuk ({{ $complaintStats['baru_masuk'] }})</option>
                        <option value="sedang_dicek" @selected($complaintStatus === 'sedang_dicek')>Sedang Dicek ({{ $complaintStats['sedang_dicek'] }})</option>
                        <option value="selesai" @selected($complaintStatus === 'selesai')>Selesai ({{ $complaintStats['selesai'] }})</option>
                        <option value="escalated" @selected($complaintStatus === 'escalated')>Dieskalasi ke Admin ({{ $complaintStats['escalated'] }})</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <select name="complaint_category"
                            aria-label="Filter Kategori Kendala"
                            class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-semibold text-[#13201B]">
                        <option value="all" @selected($complaintCategory === 'all')>Semua Kategori</option>
                        @foreach ($complaintCategories as $catKey => $catLabel)
                            <option value="{{ $catKey }}" @selected($complaintCategory === $catKey)>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <input type="search"
                           name="complaint_q"
                           value="{{ $complaintSearch }}"
                           placeholder="Cari tiket / kode..."
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs font-medium text-[#13201B]">
                </div>

                <div class="sm:col-span-2 flex gap-1.5">
                    <button type="submit" class="flex-1 rounded-xl bg-[#0C392F] px-3 py-2 text-xs font-bold text-white hover:bg-[#0C5A3E] transition">
                        Filter
                    </button>
                </div>
            </form>

            {{-- Daftar Tiket --}}
            @if ($complaints->isEmpty())
                <div class="rounded-xl border border-dashed border-[#DEDAC9] bg-[#F6F4EC]/70 p-8 text-center">
                    <p class="text-sm font-bold text-[#0C392F]">Belum ada tiket kendala yang sesuai filter.</p>
                    <p class="text-xs text-[#5C6862] mt-1">Gunakan formulir di samping untuk mencatat laporan kendala baru dari pelanggan.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($complaints as $complaint)
                        <article class="rounded-2xl border {{ $complaint->is_escalated ? 'border-purple-300 bg-purple-50/25' : 'border-[#DEDAC9] bg-white' }} p-4 sm:p-5 space-y-3">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-mono text-xs font-extrabold text-[#0C392F]">{{ $complaint->ticket_code }}</span>
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $complaintStatusBadge($complaint->status) }}">
                                            {{ $complaint->statusLabel() }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full bg-[#F6F4EC] border border-[#DEDAC9] px-2.5 py-0.5 text-[11px] font-bold text-[#4B5D55]">
                                            {{ $complaint->categoryLabel() }}
                                        </span>
                                        @if ($complaint->is_escalated)
                                            <span class="inline-flex items-center rounded-full border border-purple-300 bg-purple-100 px-2.5 py-0.5 text-[11px] font-extrabold text-purple-900">
                                                Dieskalasi ke Admin
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#5C6862]">
                                        @if ($complaint->booking_code)
                                            Booking: <strong class="font-mono text-[#13201B]">{{ $complaint->booking_code }}</strong> &middot;
                                        @endif
                                        @if ($complaint->customer_name)
                                            Pelanggan: <strong class="text-[#13201B]">{{ $complaint->customer_name }}</strong>
                                            @if ($complaint->customer_phone)
                                                ({{ $complaint->customer_phone }})
                                            @endif
                                            &middot;
                                        @endif
                                        Dicatat {{ $complaint->created_at?->translatedFormat('d M Y H:i') }} WIB
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-xl bg-[#F6F4EC]/70 p-3 text-sm text-[#13201B]">
                                {{ $complaint->description }}
                            </div>

                            @if ($complaint->is_escalated)
                                <div class="rounded-xl border border-purple-200 bg-purple-50 px-3.5 py-2.5 text-xs text-purple-900">
                                    <span class="font-extrabold">Catatan Eskalasi ke Admin:</span>
                                    {{ $complaint->escalation_reason }}
                                    @if ($complaint->escalated_at)
                                        <span class="block text-[11px] text-purple-700 mt-0.5">
                                            Dieskalasi {{ $complaint->escalated_at->translatedFormat('d M Y H:i') }} WIB
                                            @if ($complaint->escalatedBy)
                                                oleh {{ $complaint->escalatedBy->name }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Form Update Status & Catatan Tindak Lanjut --}}
                            <form method="POST" action="{{ route('dashboard.customer-service.complaints.update', $complaint) }}" class="space-y-3 border-t border-[#ECE8DA] pt-3">
                                @csrf
                                @method('PATCH')
                                @if ($complaintStatus !== 'all')
                                    <input type="hidden" name="redirect_status" value="{{ $complaintStatus }}">
                                @endif
                                @if ($complaintCategory !== 'all')
                                    <input type="hidden" name="redirect_category" value="{{ $complaintCategory }}">
                                @endif
                                @if ($complaintSearch !== '')
                                    <input type="hidden" name="redirect_q" value="{{ $complaintSearch }}">
                                @endif

                                <div>
                                    <label for="notes-{{ $complaint->id }}" class="block text-[11px] font-bold uppercase tracking-wider text-[#5C6862] mb-1">
                                        Catatan Tindak Lanjut / Solusi
                                    </label>
                                    <textarea id="notes-{{ $complaint->id }}"
                                              name="follow_up_notes"
                                              rows="2"
                                              placeholder="Catat hasil pengecekan, koordinasi dengan sopir, atau solusi yang diberikan..."
                                              class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3 py-2 text-xs text-[#13201B] focus:border-[#0C7950] focus:outline-none">{{ $complaint->follow_up_notes }}</textarea>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @foreach ($complaintStatuses as $statusKey => $statusLabel)
                                            <button type="submit"
                                                    name="status"
                                                    value="{{ $statusKey }}"
                                                    class="rounded-lg border px-3 py-1.5 text-xs font-bold transition {{ $complaint->status === $statusKey ? 'border-[#0C7950] bg-[#0C7950] text-white' : 'border-[#DEDAC9] bg-[#F6F4EC] text-[#4B5D55] hover:bg-[#ECE8DA]' }}">
                                                {{ $statusLabel }}
                                            </button>
                                        @endforeach
                                    </div>

                                    @if (! $complaint->is_escalated)
                                        <button type="button"
                                                onclick="toggleEscalationBox({{ $complaint->id }})"
                                                class="inline-flex items-center gap-1 rounded-lg border border-purple-300 bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-800 hover:bg-purple-100 transition">
                                            Eskalasi ke Admin
                                        </button>
                                    @endif
                                </div>
                            </form>

                            {{-- Form Eskalasi ke Admin (Collapsible) --}}
                            @if (! $complaint->is_escalated)
                                <form id="escalate-box-{{ $complaint->id }}"
                                      method="POST"
                                      action="{{ route('dashboard.customer-service.complaints.escalate', $complaint) }}"
                                      class="hidden rounded-xl border border-purple-200 bg-purple-50/60 p-3.5 space-y-2.5">
                                    @csrf
                                    @if ($complaintStatus !== 'all')
                                        <input type="hidden" name="redirect_status" value="{{ $complaintStatus }}">
                                    @endif
                                    @if ($complaintCategory !== 'all')
                                        <input type="hidden" name="redirect_category" value="{{ $complaintCategory }}">
                                    @endif
                                    @if ($complaintSearch !== '')
                                        <input type="hidden" name="redirect_q" value="{{ $complaintSearch }}">
                                    @endif
                                    <label for="escalate-reason-{{ $complaint->id }}" class="block text-xs font-bold text-purple-900">
                                        Alasan Eskalasi ke Admin (mis. pengajuan refund / ganti rugi)
                                    </label>
                                    <textarea id="escalate-reason-{{ $complaint->id }}"
                                              name="escalation_reason"
                                              rows="2"
                                              required
                                              placeholder="Jelaskan mengapa kendala ini perlu ditangani Admin..."
                                              class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-xs text-[#13201B] focus:border-purple-500 focus:outline-none"></textarea>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                onclick="toggleEscalationBox({{ $complaint->id }})"
                                                class="rounded-lg border border-[#DEDAC9] bg-white px-3 py-1.5 text-xs font-bold text-[#5C6862]">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                class="rounded-lg bg-purple-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-purple-800 transition">
                                            Kirim Eskalasi ke Admin
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function toggleEscalationBox(complaintId) {
        const box = document.getElementById('escalate-box-' + complaintId);
        if (!box) return;
        box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) {
            const textarea = document.getElementById('escalate-reason-' + complaintId);
            if (textarea) textarea.focus();
        }
    }
</script>
@endpush
