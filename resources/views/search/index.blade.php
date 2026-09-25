@extends('layouts.app')

@section('title', 'Cari Travel - TravelKu')

@section('content')

<section class="search-page">

    <div class="container">

        <div class="search-page-header">

            <span class="section-label">
                Cari Perjalanan
            </span>

            <h1>
                Temukan Jadwal Travel
            </h1>

            <p>
                Pilih rute dan tanggal perjalanan
                yang Anda inginkan.
            </p>

        </div>


        {{-- SEARCH FORM --}}

        <div class="search-page-card">

            <form
                action="{{ route('search') }}"
                method="GET"
            >

                <div class="search-form-grid">

                    <div class="form-group">

                        <label for="origin">
                            Dari
                        </label>

                        <select
                            name="origin"
                            id="origin"
                            class="form-control"
                            required
                        >
                            <option value="">
                                Pilih kota asal
                            </option>

                            @foreach ($locations as $location)

                                <option
                                    value="{{ $location->id }}"
                                    @selected(
                                        request('origin') == $location->id
                                    )
                                >
                                    {{ $location->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="destination">
                            Ke
                        </label>

                        <select
                            name="destination"
                            id="destination"
                            class="form-control"
                            required
                        >
                            <option value="">
                                Pilih kota tujuan
                            </option>

                            @foreach ($locations as $location)

                                <option
                                    value="{{ $location->id }}"
                                    @selected(
                                        request('destination') == $location->id
                                    )
                                >
                                    {{ $location->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="departure_date">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="departure_date"
                            id="departure_date"
                            class="form-control"
                            value="{{ request('departure_date') }}"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="passengers">
                            Penumpang
                        </label>

                        <select
                            name="passengers"
                            id="passengers"
                            class="form-control"
                            required
                        >

                            @for ($i = 1; $i <= 10; $i++)

                                <option
                                    value="{{ $i }}"
                                    @selected(
                                        request('passengers', 1) == $i
                                    )
                                >
                                    {{ $i }} Penumpang
                                </option>

                            @endfor

                        </select>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary search-button"
                >
                    Cari Travel
                </button>

            </form>

        </div>


        {{-- VALIDATION ERROR --}}

        @if ($errors->any())

            <div class="alert alert-error">

                <strong>
                    Pencarian tidak dapat dilakukan.
                </strong>

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- SEARCH RESULT --}}

        @if ($searchPerformed)

            <div class="search-result">

                <div class="result-header">

                    <div>

                        <span class="section-label">
                            Hasil Pencarian
                        </span>

                        <h2>
                            Jadwal Tersedia
                        </h2>

                    </div>

                    <span class="result-count">
                        {{ $schedules->count() }} perjalanan
                    </span>

                </div>


                @if ($schedules->count() > 0)

                    <div class="schedule-list">

                        @foreach ($schedules as $schedule)

                            <article class="schedule-card">

                                <div class="schedule-route">

                                    <span>
                                        {{ $schedule->route->origin->name }}
                                    </span>

                                    <strong>
                                        →
                                    </strong>

                                    <span>
                                        {{ $schedule->route->destination->name }}
                                    </span>

                                </div>


                                <div class="schedule-info">

                                    <div>

                                        <small>
                                            Keberangkatan
                                        </small>

                                        <strong>
                                            {{ $schedule->departure_time }}
                                        </strong>

                                    </div>


                                    <div>

                                        <small>
                                            Durasi
                                        </small>

                                        <strong>
                                            {{ $schedule->route->duration }}
                                        </strong>

                                    </div>


                                    <div>

                                        <small>
                                            Harga
                                        </small>

                                        <strong>
                                            Rp {{ number_format(
                                                $schedule->route->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}
                                        </strong>

                                    </div>


                                    <div>

                                        <small>
                                            Kursi tersedia
                                        </small>

                                        <strong>
                                            {{ $schedule->available_seats }}
                                        </strong>

                                    </div>

                                </div>


                                <div class="schedule-action">
                                    @auth
                                        <a href="{{ route('dashboard.user.booking.pilih-kursi', ['schedule' => $schedule->id, 'passengers' => request('passengers', 1)]) }}"
                                           class="btn btn-primary" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;">
                                            <span>Pilih Kursi</span>
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    @else
                                        <a href="{{ route('user.login') }}?redirect={{ urlencode(route('dashboard.user.booking.pilih-kursi', ['schedule' => $schedule->id, 'passengers' => request('passengers', 1)])) }}"
                                           class="btn btn-primary" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;">
                                            <span>Pesan Tiket</span>
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    @endauth
                                </div>

                            </article>

                        @endforeach

                    </div>

                @else

                    <div class="empty-result" style="padding: 40px 20px; text-align: center;">

                        @if(!($routeConnected ?? false))
                            <div style="font-size: 2.2rem; margin-bottom: 10px;">⚠️</div>
                            <h3 style="margin-bottom: 8px; font-weight: 800;">
                                Jadwal Shuttle Belum Tersedia
                            </h3>
                            <p style="max-width: 540px; margin: 0 auto 16px auto; color: #475569; line-height: 1.6; font-size: 0.95rem;">
                                Mohon maaf, rute langsung dari <strong>{{ $originLoc?->name ?? 'Kota Asal' }}</strong> ke <strong>{{ $destLoc?->name ?? 'Kota Tujuan' }}</strong> saat ini belum tersedia. Silakan pilih rute transit atau hub terdekat.
                            </p>

                            @if($transitHub)
                                <div style="background: #e6fcf5; border: 1px solid #b2f2bb; border-radius: 12px; padding: 14px 18px; max-width: 520px; margin: 0 auto 16px auto; text-align: left;">
                                    <strong style="color: #087f55; display: block; margin-bottom: 4px; font-size: 0.85rem;">
                                        💡 Rekomendasi Transit via {{ $transitHub->name }}:
                                    </strong>
                                    <p style="font-size: 0.8rem; color: #2b8a3e; margin: 0 0 10px 0;">
                                        Anda dapat bepergian melalui 2 ritase terhubung via hub transit <strong>{{ $transitHub->city }}</strong>:
                                    </p>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <a href="{{ route('search', ['origin' => $originLoc->id, 'destination' => $transitHub->id, 'departure_date' => request('departure_date', date('Y-m-d')), 'passengers' => request('passengers', 1)]) }}"
                                           style="padding: 6px 12px; border-radius: 8px; background: #ffffff; border: 1px solid #b2f2bb; color: #087f55; text-decoration: none; font-size: 0.8rem; font-weight: 700;">
                                            1. {{ $originLoc->city }} ➔ {{ $transitHub->city }}
                                        </a>
                                        <a href="{{ route('search', ['origin' => $transitHub->id, 'destination' => $destLoc->id, 'departure_date' => request('departure_date', date('Y-m-d')), 'passengers' => request('passengers', 1)]) }}"
                                           style="padding: 6px 12px; border-radius: 8px; background: #ffffff; border: 1px solid #b2f2bb; color: #087f55; text-decoration: none; font-size: 0.8rem; font-weight: 700;">
                                            2. {{ $transitHub->city }} ➔ {{ $destLoc->city }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; max-width: 520px; margin: 0 auto; text-align: left; font-size: 0.85rem;">
                                <strong style="color: #334155; display: block; margin-bottom: 6px;">Standar Koridor Shuttle Aktif (Jadwal Selalu Tersedia):</strong>
                                <span style="color: #64748b; line-height: 1.6;">
                                    Bandung ⇄ Jakarta (Semanggi, Tebet, Cikini) • Bandung ⇄ Bekasi (Summarecon) • Bandung ⇄ Bogor (Baranangsiang) • Bandung ⇄ Depok (Margonda) • Bandung ⇄ Bandara Soetta (CGK) • Bandung ⇄ Cianjur ⇄ Bogor • Sukabumi ⇄ Bogor / Jakarta • Bandung ⇄ Cirebon &amp; Jakarta ⇄ Cirebon.
                                </span>
                            </div>
                        @else
                            <div style="font-size: 2.2rem; margin-bottom: 10px;">🚐</div>
                            <h3 style="margin-bottom: 8px; font-weight: 800;">
                                Seluruh Kursi Telah Terisi
                            </h3>
                            <p style="max-width: 450px; margin: 0 auto; color: #64748b;">
                                Kursi untuk {{ request('passengers', 1) }} penumpang pada tanggal yang dipilih sedang penuh. Silakan pilih tanggal atau jam lainnya.
                            </p>
                        @endif

                    </div>

                @endif

            </div>

        @endif

    </div>

</section>

@endsection