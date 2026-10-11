{{--
    Checkout progress, written like the KM posts on the landing page.
    Expects: $current (1 = kursi, 2 = data, 3 = bayar).
--}}
@php
    $steps = [1 => 'Pilih kursi', 2 => 'Data pemesan', 3 => 'Bayar'];
@endphp

<nav aria-label="Langkah pemesanan">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-2 text-sm font-semibold">
        @foreach ($steps as $number => $label)
            @php
                $isDone = $number < $current;
                $isCurrent = $number === $current;
            @endphp
            <li class="flex items-center gap-2" @if ($isCurrent) aria-current="step" @endif>
                <span @class([
                    'inline-flex flex-col items-center justify-center w-9 h-9 rounded-md border-2 leading-none',
                    'border-brand-800 bg-brand-800 text-white' => $isCurrent,
                    'border-brand-700 text-brand-700 bg-white' => $isDone,
                    'border-slate-300 text-slate-500 bg-white' => ! $isCurrent && ! $isDone,
                ])>
                    <span class="text-[10px] font-bold">KM</span>
                    <span class="text-sm font-extrabold">{{ $number }}</span>
                </span>
                <span @class([
                    'text-brand-900' => $isCurrent,
                    'text-brand-700' => $isDone,
                    'text-slate-500' => ! $isCurrent && ! $isDone,
                ])>
                    {{ $label }}
                    @if ($isDone)
                        <span class="sr-only">(selesai)</span>
                    @endif
                </span>
                @unless ($loop->last)
                    <span class="w-6 sm:w-10 border-t-2 border-dashed border-slate-300" aria-hidden="true"></span>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
