{{--
    Fields shared by the add and edit forms for a saved passenger.
    Expects: $savedPassenger (or null), $prefix (unique per form, for label ids).
--}}
@php
    $fieldClass = 'w-full min-h-12 rounded-lg border-[1.5px] border-[#7D7969] bg-white px-4 text-slate-900';
@endphp

<div class="space-y-1.5 sm:col-span-2">
    <label for="{{ $prefix }}-name" class="block font-semibold text-slate-900">Nama lengkap</label>
    <input id="{{ $prefix }}-name" type="text" name="name" required maxlength="100" value="{{ $savedPassenger?->name }}" placeholder="Nama sesuai KTP" class="{{ $fieldClass }}">
</div>
<div class="space-y-1.5">
    <label for="{{ $prefix }}-phone" class="block font-semibold text-slate-900">Nomor HP <span class="font-normal text-slate-600">(boleh kosong)</span></label>
    <input id="{{ $prefix }}-phone" type="tel" name="phone" maxlength="25" inputmode="tel" value="{{ $savedPassenger?->phone }}" placeholder="08xxxxxxxxxx" class="{{ $fieldClass }}">
</div>
<div class="space-y-1.5">
    <label for="{{ $prefix }}-nik" class="block font-semibold text-slate-900">NIK <span class="font-normal text-slate-600">(boleh kosong)</span></label>
    <input id="{{ $prefix }}-nik" type="text" name="nik" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" value="{{ $savedPassenger?->nik }}" aria-describedby="{{ $prefix }}-nik-hint" class="{{ $fieldClass }}">
    <p id="{{ $prefix }}-nik-hint" class="text-sm text-slate-600">16 digit angka.</p>
</div>
<label class="sm:col-span-2 inline-flex items-center gap-3 min-h-11 cursor-pointer text-slate-800">
    <input type="checkbox" name="is_primary" value="1" class="w-5 h-5 accent-[#0C5A3E]" @checked($savedPassenger?->is_primary)>
    Jadikan penumpang utama (dipilih pertama saat memesan)
</label>
