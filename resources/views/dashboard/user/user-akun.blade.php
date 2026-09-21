@extends('layouts.dashboard')

@section('page-title', 'Akun & Profil')

@section('content')
{{-- x-data harus membungkus SELURUH konten termasuk modal --}}
<div x-data="{ showEditProfile: false, showChangePassword: false }">

<div class="space-y-5 max-w-2xl mx-auto">

    <!-- ===== HEADER PROFIL ===== -->
    <div style="background: linear-gradient(135deg, #087f55 0%, #066b47 100%);" class="rounded-2xl p-6 text-white flex items-center gap-5 shadow-sm">
        <!-- Avatar -->
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold text-white flex-shrink-0"
             style="background: rgba(255,255,255,.20);">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="font-bold text-xl leading-tight truncate">{{ auth()->user()->name ?? 'Nama Pengguna' }}</h2>
            <p class="text-sm mt-0.5 truncate" style="color:rgba(255,255,255,.75);">{{ auth()->user()->email ?? 'user@email.com' }}</p>
            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.65);">{{ auth()->user()->phone ?? '+62 812-xxxx-xxxx' }}</p>
        </div>
        <button id="btn-edit-profile" @click="showEditProfile = true"
            class="flex-shrink-0 p-2.5 rounded-xl transition"
            style="background: rgba(255,255,255,.18);"
            onmouseover="this.style.background='rgba(255,255,255,.28)'"
            onmouseout="this.style.background='rgba(255,255,255,.18)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
            </svg>
        </button>
    </div>

    <!-- ===== PENUMPANG TERSIMPAN ===== -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#e3f5ee;">
                    <svg width="18" height="18" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">Penumpang Tersimpan</p>
                    <p class="text-xs text-slate-400">Percepat proses booking</p>
                </div>
            </div>
            <button id="btn-tambah-penumpang" class="text-xs font-semibold hover:underline" style="color:#087f55;">+ Tambah</button>
        </div>

        <!-- Item Penumpang -->
        <div class="divide-y divide-slate-100">
            <div class="px-5 py-3 flex items-center justify-between" id="penumpang-1">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">B</div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Budi Santoso</p>
                        <p class="text-xs text-slate-400">NIK: 3273xxxxxxxxxxxxxx</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#e3f5ee; color:#087f55;">Utama</span>
                    <button class="text-slate-300 hover:text-slate-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-5 py-3 flex items-center justify-between" id="penumpang-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-xs font-bold text-pink-500">S</div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Sari Dewi</p>
                        <p class="text-xs text-slate-400">NIK: 3273xxxxxxxxxxxxxx</p>
                    </div>
                </div>
                <button class="text-slate-300 hover:text-slate-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ===== VOUCHER SAYA ===== -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg width="18" height="18" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m9 9 6 6m0-6-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">Voucher & Promo Saya</p>
                    <p class="text-xs text-slate-400">1 voucher aktif</p>
                </div>
            </div>
            <a href="{{ route('dashboard.user.user-promo') }}" class="text-xs font-semibold hover:underline" style="color:#087f55;">Lihat Semua</a>
        </div>

        <div class="px-5 py-3 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900 font-mono tracking-wider">HEMATLIBURAN</p>
                <p class="text-xs text-slate-500 mt-0.5">Diskon 25% • Berlaku s/d 30 Des 2026</p>
            </div>
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border" style="background:#e3f5ee; color:#087f55; border-color:#c7d2fe;">Aktif</span>
        </div>
    </div>

    <!-- ===== PUSAT BANTUAN ===== -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#e3f5ee;">
                    <svg width="18" height="18" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">Pusat Bantuan</p>
                    <p class="text-xs text-slate-400">Hubungi kami kapan saja</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            <a href="https://wa.me/62812345678" target="_blank" id="btn-whatsapp-cs"
               class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50 transition group">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800">Chat WhatsApp CS</p>
                    <p class="text-xs text-slate-500">Respon cepat, 08:00–22:00 WIB</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </a>

            <a href="#" id="btn-live-chat"
               class="px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50 transition group">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e3f5ee;">
                    <svg width="16" height="16" fill="none" stroke="#087f55" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800">Live Chat</p>
                    <p class="text-xs text-slate-500">Obrolan langsung dalam aplikasi</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    <span class="text-[10px] font-semibold text-emerald-600">Online</span>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </a>
        </div>
    </div>

    <!-- ===== PENGATURAN KEAMANAN ===== -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg width="18" height="18" fill="none" stroke="#68766f" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <p class="font-semibold text-slate-900 text-sm">Keamanan Akun</p>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            <button id="btn-ganti-password" @click="showChangePassword = true"
               class="w-full px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50 transition text-left group">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800">Ganti Password</p>
                    <p class="text-xs text-slate-500">Perbarui kata sandi akun Anda</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <button id="btn-notif-setting"
               class="w-full px-5 py-3.5 flex items-center gap-4 hover:bg-slate-50 transition text-left group">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9m-4.27 13a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800">Pengaturan Notifikasi</p>
                    <p class="text-xs text-slate-500">Kelola preferensi notifikasi</p>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>

    <!-- ===== LOGOUT ===== -->
    <div class="bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button id="btn-logout" type="submit"
                class="w-full px-5 py-4 flex items-center gap-4 hover:bg-rose-50 transition text-left group">
                <div class="w-9 h-9 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-rose-600">Keluar dari Akun</p>
                    <p class="text-xs text-slate-400">Sesi Anda akan diakhiri</p>
                </div>
                <svg class="w-4 h-4 text-rose-300 group-hover:text-rose-500 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </form>
    </div>

    <!-- Versi App -->
    <p class="text-center text-[11px] text-slate-300 pb-4">FastShuttle v2.1.0 • Portal Penumpang</p>

</div>

{{-- ===== MODAL: EDIT PROFIL ===== --}}
{{-- Modal ada di DALAM x-data wrapper agar Alpine bisa membaca state --}}
<div x-show="showEditProfile"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 pb-6 sm:pb-0"
     style="background: rgba(0,0,0,.45); backdrop-filter: blur(4px);"
     @click.self="showEditProfile = false">

    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-900">Edit Profil</h3>
            <button id="modal-edit-close" @click="showEditProfile = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-3 mb-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" id="edit-nama" value="{{ auth()->user()->name ?? '' }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    style="transition:.15s;" onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                <input type="email" id="edit-email" value="{{ auth()->user()->email ?? '' }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor HP</label>
                <input type="tel" id="edit-phone" placeholder="+62 812-xxxx-xxxx"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
        </div>

        <div class="flex gap-3">
            <button @click="showEditProfile = false"
                class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">
                Batal
            </button>
            <button id="btn-simpan-profil"
                class="flex-1 py-2.5 text-white text-sm font-semibold rounded-xl transition"
                style="background:#087f55;"
                onmouseover="this.style.background='#066b47'"
                onmouseout="this.style.background='#087f55'">
                Simpan
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL: GANTI PASSWORD ===== --}}
<div x-show="showChangePassword"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 pb-6 sm:pb-0"
     style="background: rgba(0,0,0,.45); backdrop-filter: blur(4px);"
     @click.self="showChangePassword = false">

    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-900">Ganti Password</h3>
            <button id="modal-password-close" @click="showChangePassword = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-3 mb-5">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Password Saat Ini</label>
                <input type="password" id="password-current"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Password Baru</label>
                <input type="password" id="password-new"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                <input type="password" id="password-confirm"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm outline-none"
                    onfocus="this.style.borderColor='#087f55'; this.style.boxShadow='0 0 0 3px rgba(8,127,85,.12)'" onblur="this.style.borderColor=''; this.style.boxShadow=''">
            </div>
        </div>

        <div class="flex gap-3">
            <button @click="showChangePassword = false"
                class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">
                Batal
            </button>
            <button id="btn-simpan-password"
                class="flex-1 py-2.5 text-white text-sm font-semibold rounded-xl transition"
                style="background:#087f55;"
                onmouseover="this.style.background='#066b47'"
                onmouseout="this.style.background='#087f55'">
                Simpan
            </button>
        </div>
    </div>
</div>

</div>{{-- end x-data wrapper --}}
@endsection

