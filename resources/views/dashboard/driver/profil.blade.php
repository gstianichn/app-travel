@extends('layouts.driver')

@section('title', 'Profil Saya - Driver TravelKu')

@section('content')
<div class="space-y-6 text-[#13201B]">
    <header>
        <h1 class="text-2xl font-extrabold text-[#0C392F]">Profil Saya</h1>
        <p class="text-sm text-[#5C6862] mt-1">
            Kelola data pribadi, kontak darurat, dan keamanan kata sandi akun pengemudi Anda.
        </p>
    </header>

    {{-- Kartu Ringkasan Driver --}}
    <section class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#0C392F] text-lg font-extrabold text-[#FFFDF8]">
                    {{ strtoupper(substr($user->name ?? 'D', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-[#0C392F]">{{ $user->name }}</h2>
                    <p class="text-xs text-[#5C6862] mt-0.5">{{ $user->email }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-[#B8DFC9] bg-[#EEF6F1] px-2.5 py-0.5 text-[11px] font-bold text-[#0C5A3E]">
                            {{ $driver->statusLabel() }}
                        </span>
                        @if ($driver->license_number)
                            <span class="inline-flex items-center rounded-full border border-[#DEDAC9] bg-[#F6F4EC] px-2.5 py-0.5 text-[11px] font-semibold text-[#4B5D55]">
                                SIM: {{ $driver->license_number }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:min-w-[220px]">
                <div class="rounded-xl border border-[#ECE8DA] bg-[#F6F4EC]/70 px-3 py-2.5 text-center">
                    <p class="text-lg font-extrabold text-[#0C392F] tabular-nums">{{ $totalCompletedTrips ?? 0 }}</p>
                    <p class="text-[11px] font-semibold text-[#5C6862]">Ritase Selesai</p>
                </div>
                <div class="rounded-xl border border-[#ECE8DA] bg-[#F6F4EC]/70 px-3 py-2.5 text-center">
                    <p class="text-sm font-extrabold text-[#0C392F] pt-0.5">{{ $activeSchedule->vehicle?->plate_number ?? '-' }}</p>
                    <p class="text-[11px] font-semibold text-[#5C6862] mt-0.5">Armada Aktif</p>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Form Informasi Profil --}}
        <section class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs space-y-4">
            <div>
                <h2 class="text-base font-extrabold text-[#0C392F]">Informasi Profil</h2>
                <p class="text-xs text-[#5C6862] mt-0.5">Perbarui nama, email, nomor HP, alamat, dan kontak darurat.</p>
            </div>

            <form method="POST" action="{{ route('dashboard.driver.profil.update') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="driver-profile-name" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Nama Lengkap <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="driver-profile-name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="driver-profile-email" class="block text-xs font-bold text-[#0C392F] mb-1">
                            Alamat Email <span class="text-rose-600">*</span>
                        </label>
                        <input type="email"
                               id="driver-profile-email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="driver-profile-phone" class="block text-xs font-bold text-[#0C392F] mb-1">
                            Nomor HP
                        </label>
                        <input type="text"
                               id="driver-profile-phone"
                               name="phone"
                               value="{{ old('phone', $driver->phone ?: $user->phone) }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                        @error('phone')
                            <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="driver-profile-address" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Alamat Domisili
                    </label>
                    <input type="text"
                           id="driver-profile-address"
                           name="address"
                           value="{{ old('address', $driver->address) }}"
                           placeholder="Alamat tempat tinggal"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="driver-emergency-name" class="block text-xs font-bold text-[#0C392F] mb-1">
                            Nama Kontak Darurat
                        </label>
                        <input type="text"
                               id="driver-emergency-name"
                               name="emergency_contact_name"
                               value="{{ old('emergency_contact_name', $driver->emergency_contact_name) }}"
                               placeholder="Keluarga / kerabat"
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    </div>

                    <div>
                        <label for="driver-emergency-phone" class="block text-xs font-bold text-[#0C392F] mb-1">
                            No. HP Kontak Darurat
                        </label>
                        <input type="text"
                               id="driver-emergency-phone"
                               name="emergency_contact_phone"
                               value="{{ old('emergency_contact_phone', $driver->emergency_contact_phone) }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" class="rounded-xl bg-[#0C7950] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#0C5A3E] transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        {{-- Form Ubah Password --}}
        <section class="rounded-2xl border border-[#DEDAC9] bg-[#FFFDF8] p-6 shadow-xs space-y-4">
            <div>
                <h2 class="text-base font-extrabold text-[#0C392F]">Ubah Password</h2>
                <p class="text-xs text-[#5C6862] mt-0.5">Gunakan kata sandi minimal 8 karakter untuk menjaga keamanan akun.</p>
            </div>

            <form method="POST" action="{{ route('dashboard.driver.profil.password') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="driver-current-password" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Password Saat Ini <span class="text-rose-600">*</span>
                    </label>
                    <input type="password"
                           id="driver-current-password"
                           name="current_password"
                           required
                           autocomplete="current-password"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    @error('current_password')
                        <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="driver-new-password" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Password Baru <span class="text-rose-600">*</span>
                    </label>
                    <input type="password"
                           id="driver-new-password"
                           name="password"
                           required
                           autocomplete="new-password"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="driver-confirm-password" class="block text-xs font-bold text-[#0C392F] mb-1">
                        Konfirmasi Password Baru <span class="text-rose-600">*</span>
                    </label>
                    <input type="password"
                           id="driver-confirm-password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="w-full rounded-xl border border-[#DEDAC9] bg-white px-3.5 py-2.5 text-sm font-medium text-[#13201B] focus:border-[#0C7950] focus:outline-none">
                </div>

                <div class="pt-1">
                    <button type="submit" class="rounded-xl bg-[#0C392F] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#0C5A3E] transition">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
