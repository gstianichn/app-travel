@extends('layouts.driver')

@section('title', 'Lapor Kendala & Ganti Mobil - Driver TravelKu')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="text-2xl font-extrabold text-[#0C392F]">Lapor Kendala &amp; Ganti Mobil</h1>
        <p class="text-sm text-[#5C6862] mt-1">
            Laporan darurat langsung aktif di sistem tanpa menunggu persetujuan admin, otomatis mengirim notifikasi ke Admin &amp; rekan driver.
        </p>
    </header>

    {{-- Banner Kontak Cepat Rekan Driver (langsung disorot setelah laporan dikirim) --}}
    @if (session('incident_reported'))
        <section class="rounded-2xl border-2 border-red-400 bg-red-50 p-5 space-y-3" aria-label="Koordinasi oper penumpang darurat">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full bg-red-600 text-white text-[11px] font-extrabold uppercase">
                    Laporan Darurat Aktif
                </span>
                <h2 class="text-lg font-extrabold text-red-950 mt-1">
                    Segera Hubungi Rekan Driver di Bawah untuk Oper Penumpang
                </h2>
                <p class="text-xs text-red-800">
                    Admin dan seluruh rekan driver sudah menerima notifikasi darurat. Anda bisa langsung menelepon atau mengirim WhatsApp ke salah satu rekan driver di bawah ini.
                </p>
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Form Laporan Cepat --}}
        <section class="lg:col-span-6 bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[#ECE8DA]">
                <h2 class="text-base font-extrabold text-[#0C392F]">Formulir Laporan Darurat</h2>
                <p class="text-xs text-[#5C6862]">
                    Unit aktif: <strong>{{ $activeSchedule->vehicle?->name ?? 'Unit Shuttle' }}</strong> ({{ $activeSchedule->vehicle?->plate_number ?? '-' }})
                </p>
            </div>

            <form method="POST" action="{{ route('dashboard.driver.incident.instant') }}" class="p-5 space-y-4">
                @csrf
                @if ($activeSchedule->exists)
                    <input type="hidden" name="schedule_id" value="{{ $activeSchedule->id }}">
                @endif

                {{-- Pilihan Jenis Kendala --}}
                <div>
                    <label class="block text-xs font-extrabold text-[#13201B] mb-2">Jenis Kendala</label>
                    <div class="grid grid-cols-1 gap-2.5">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-[#DEDAC9] bg-white cursor-pointer hover:border-[#0C7950]">
                            <input type="radio" name="category" value="mesin_mogok" {{ old('category', 'mesin_mogok') === 'mesin_mogok' ? 'checked' : '' }} required class="accent-[#0C7950] w-4 h-4">
                            <div>
                                <div class="text-sm font-bold text-[#13201B]">Mobil Mogok / Kerusakan Mesin</div>
                                <div class="text-xs text-[#5C6862]">Status unit langsung ditandai perbaikan &amp; minta mobil pengganti.</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-[#DEDAC9] bg-white cursor-pointer hover:border-[#0C7950]">
                            <input type="radio" name="category" value="ban_bocor" {{ old('category') === 'ban_bocor' ? 'checked' : '' }} required class="accent-[#0C7950] w-4 h-4">
                            <div>
                                <div class="text-sm font-bold text-[#13201B]">Ban Bocor / Insiden</div>
                                <div class="text-xs text-[#5C6862]">Kendala ban atau insiden di jalan yang menghambat perjalanan.</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-[#DEDAC9] bg-white cursor-pointer hover:border-[#0C7950]">
                            <input type="radio" name="category" value="driver_sakit" {{ old('category') === 'driver_sakit' ? 'checked' : '' }} required class="accent-[#0C7950] w-4 h-4">
                            <div>
                                <div class="text-sm font-bold text-[#13201B]">Driver Mendadak Sakit</div>
                                <div class="text-xs text-[#5C6862]">Butuh pengemudi pengganti segera untuk melanjutkan perjalanan.</div>
                            </div>
                        </label>
                    </div>
                    @error('category')
                        <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi Kejadian Terkini + Tombol GPS Otomatis --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <label for="incident_location" class="text-xs font-extrabold text-[#13201B]">Lokasi Kejadian Terkini</label>
                        <button type="button"
                                id="btnGrabIncidentGps"
                                onclick="grabDriverGpsCoords()"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#EEF6F1] border border-[#cde3d7] text-xs font-bold text-[#0C5A3E] hover:bg-[#DFF0E8] transition cursor-pointer">
                            Ambil Koordinat GPS Otomatis
                        </button>
                    </div>
                    <input id="incident_location"
                           type="text"
                           name="location_name"
                           value="{{ old('location_name') }}"
                           required
                           placeholder="Contoh: Mobil Toyota HiAce mogok di daerah Tol Cipularang KM 88"
                           class="w-full h-11 px-3.5 rounded-xl border border-[#DEDAC9] bg-white text-sm text-[#13201B] focus:outline-none focus:border-[#0C7950]">
                    @error('location_name')
                        <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-2 gap-2.5 mt-2">
                        <input id="incident_lat" type="text" name="latitude" value="{{ old('latitude') }}" readonly placeholder="Latitude GPS" class="h-9 px-3 rounded-lg border border-[#ECE8DA] bg-[#F6F4EC] text-xs font-mono text-[#5C6862]">
                        <input id="incident_lng" type="text" name="longitude" value="{{ old('longitude') }}" readonly placeholder="Longitude GPS" class="h-9 px-3 rounded-lg border border-[#ECE8DA] bg-[#F6F4EC] text-xs font-mono text-[#5C6862]">
                    </div>
                    <p id="gpsGrabStatus" class="text-[11px] text-[#5C6862] mt-1"></p>
                </div>

                {{-- Keterangan Tambahan --}}
                <div>
                    <label for="incident_desc" class="block text-xs font-extrabold text-[#13201B] mb-1.5">Detail Singkat Kondisi (Opsional)</label>
                    <textarea id="incident_desc"
                              name="description"
                              rows="3"
                              placeholder="Contoh: Ada 5 penumpang di dalam mobil, posisi aman di bahu jalan KM 88 arah Jakarta."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-[#DEDAC9] bg-white text-sm text-[#13201B] focus:outline-none focus:border-[#0C7950]">{{ old('description') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-5 rounded-xl bg-red-600 text-white text-sm font-extrabold hover:bg-red-700 transition cursor-pointer">
                    Kirim Laporan Darurat Sekarang (Langsung Aktif)
                </button>
            </form>
        </section>

        {{-- Daftar Kontak Cepat Rekan Driver (Untuk Oper Penumpang) --}}
        <section class="lg:col-span-6 space-y-6">
            <div class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[#ECE8DA] flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-extrabold text-[#0C392F]">Kontak Cepat Rekan Driver Hari Ini</h2>
                        <p class="text-xs text-[#5C6862]">Hubungi rekan driver yang bertugas atau standby untuk oper penumpang.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-[#EEF6F1] text-[#0C5A3E] text-xs font-bold">
                        {{ $fellowDrivers->count() }} Rekan
                    </span>
                </div>

                <div class="divide-y divide-[#ECE8DA]">
                    @forelse ($fellowDrivers as $colleague)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <strong class="text-sm font-extrabold text-[#13201B]">{{ $colleague->name }}</strong>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $colleague->status === 'on_duty' ? 'bg-amber-100 text-amber-800' : 'bg-[#DFF0E8] text-[#0C5A3E]' }}">
                                        {{ $colleague->status_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-[#5C6862] mt-0.5">
                                    {{ $colleague->vehicle_name }} ({{ $colleague->plate_number }}) &middot; {{ $colleague->today_route }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if (! empty($colleague->phone))
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $colleague->phone) }}"
                                       class="px-3 py-2 rounded-lg border border-[#DEDAC9] bg-white text-xs font-bold text-[#13201B] hover:bg-[#F6F4EC]">
                                        Telepon
                                    </a>
                                @endif
                                @if (! empty($colleague->wa_phone))
                                    <a href="https://wa.me/{{ $colleague->wa_phone }}?text={{ urlencode('DARURAT TravelKu: Halo '.$colleague->name.', saya '.$user->name.' mengalami kendala di jalan dan butuh bantuan oper penumpang. Mohon respons secepatnya.') }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="px-3 py-2 rounded-lg bg-[#0C7950] text-white text-xs font-bold hover:bg-[#0C5A3E]">
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-[#5C6862]">
                            Belum ada data rekan driver lain yang aktif hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Laporan Kendala Driver --}}
            @if ($recentDriverIncidents->isNotEmpty())
                <div class="bg-[#FFFDF8] border border-[#DEDAC9] rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[#ECE8DA]">
                        <h3 class="text-sm font-extrabold text-[#0C392F]">Laporan Kendala Terakhir Anda</h3>
                    </div>
                    <div class="divide-y divide-[#ECE8DA]">
                        @foreach ($recentDriverIncidents->take(5) as $inc)
                            <div class="p-4 text-xs space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <strong class="text-sm text-[#13201B]">{{ $inc->categoryLabel() }}</strong>
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold">
                                        {{ $inc->statusLabel() }}
                                    </span>
                                </div>
                                <p class="text-[#5C6862]"><strong>Lokasi:</strong> {{ $inc->location_name }}</p>
                                <p class="text-[#5C6862]">{{ $inc->reported_at ? $inc->reported_at->translatedFormat('d M Y, H:i').' WIB' : '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function grabDriverGpsCoords() {
        const statusEl = document.getElementById('gpsGrabStatus');
        const latInput = document.getElementById('incident_lat');
        const lngInput = document.getElementById('incident_lng');
        const locInput = document.getElementById('incident_location');

        if (!navigator.geolocation) {
            if (statusEl) statusEl.textContent = 'Browser tidak mendukung GPS otomatis. Silakan ketik lokasi manual.';
            return;
        }

        if (statusEl) statusEl.textContent = 'Mengambil koordinat GPS terkini...';

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                if (latInput) latInput.value = lat;
                if (lngInput) lngInput.value = lng;
                if (locInput && !locInput.value.trim()) {
                    locInput.value = `Koordinat GPS (${lat}, ${lng})`;
                }
                if (statusEl) statusEl.textContent = `Koordinat berhasil diambil: ${lat}, ${lng}`;
            },
            function () {
                if (statusEl) statusEl.textContent = 'Izin lokasi ditolak atau GPS tidak tersedia. Silakan isi lokasi secara manual.';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endpush
