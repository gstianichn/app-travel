{{-- Session feedback for the passenger pages: text first, colour second. --}}
@if (session('success'))
    <p class="rounded-xl border-2 border-brand-600 bg-brand-50 px-5 py-4 text-brand-900" role="status">
        <b>Berhasil.</b> {{ session('success') }}
    </p>
@endif

@if (session('error'))
    <p class="rounded-xl border-2 border-rose-700 bg-rose-50 px-5 py-4 text-rose-900" role="alert">
        <b>Belum berhasil.</b> {{ session('error') }}
    </p>
@elseif (request('expired'))
    <p class="rounded-xl border-2 border-rose-700 bg-rose-50 px-5 py-4 text-rose-900" role="alert">
        <b>Waktu pemesanan habis.</b> Waktu tahan kursi (15 menit) sudah habis, kursi Anda telah dilepas kembali. Silakan pilih jadwal dan kursi kembali.
    </p>
@endif

@if ($errors->any())
    <div class="rounded-xl border-2 border-rose-700 bg-rose-50 px-5 py-4 text-rose-900" role="alert">
        <p class="font-bold">Ada yang perlu diperbaiki:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
