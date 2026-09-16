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

                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                    >
                                        Pilih Travel
                                    </button>

                                </div>

                            </article>

                        @endforeach

                    </div>

                @else

                    <div class="empty-result">

                        <h3>
                            Jadwal tidak ditemukan
                        </h3>

                        <p>
                            Belum ada perjalanan yang tersedia
                            sesuai dengan pencarian Anda.
                        </p>

                    </div>

                @endif

            </div>

        @endif

    </div>

</section>

@endsection