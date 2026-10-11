{{--
    Search form shaped like a ticket. Used on the landing page and the search page.
    Expects: $locations, $heading, optional $action (defaults to the public search) and $selected = ['origin', 'destination', 'departure_date', 'passengers'].
--}}
@php
    $selected = array_merge([
        'origin' => null,
        'destination' => null,
        'departure_date' => null,
        'passengers' => 1,
    ], $selected ?? []);
@endphp

<section class="lp-search" id="cari" aria-labelledby="cari-title">
    <div class="container">
        <form class="lp-ticket" action="{{ $action ?? route('search') }}" method="GET" data-ticket>
            <div class="lp-ticket-main">
                <h2 id="cari-title">{{ $heading }}</h2>

                @if ($locations->isEmpty())
                    <p class="lp-ticket-empty">
                        Titik keberangkatan belum diisi oleh admin, jadi pencarian belum bisa dipakai. Coba buka lagi nanti.
                    </p>
                @endif

                <div class="lp-ticket-cities">
                    <div class="lp-field">
                        <label for="origin">Dari</label>
                        <select name="origin" id="origin" required>
                            <option value="">Pilih titik berangkat</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) $selected['origin'] === (string) $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="lp-swap" data-swap aria-label="Tukar titik berangkat dan tujuan">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 4 3 8l4 4M3 8h13M17 20l4-4-4-4m4 4H8" /></svg>
                    </button>

                    <div class="lp-field">
                        <label for="destination">Ke</label>
                        <select name="destination" id="destination" required>
                            <option value="">Pilih tujuan</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) $selected['destination'] === (string) $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="lp-ticket-when">
                    <div class="lp-field">
                        <label for="departure_date">Tanggal berangkat</label>
                        <input type="date" name="departure_date" id="departure_date" min="{{ now()->toDateString() }}" value="{{ $selected['departure_date'] }}" required>
                        <div class="lp-quick-dates">
                            <button type="button" data-date-offset="0">Hari ini</button>
                            <button type="button" data-date-offset="1">Besok</button>
                        </div>
                    </div>

                    <div class="lp-field">
                        <label for="passengers">Penumpang</label>
                        <select name="passengers" id="passengers" required>
                            @for ($passenger = 1; $passenger <= 10; $passenger++)
                                <option value="{{ $passenger }}" @selected((int) $selected['passengers'] === $passenger)>{{ $passenger }} orang</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <p class="lp-ticket-status" role="status" aria-live="polite" data-ticket-status></p>
            </div>

            <div class="lp-ticket-stub">
                <dl class="lp-stub-summary" data-ticket-summary>
                    <div>
                        <dt>Rute</dt>
                        <dd class="is-empty" data-summary="route">Belum dipilih</dd>
                    </div>
                    <div>
                        <dt>Tanggal</dt>
                        <dd class="is-empty" data-summary="date">Belum dipilih</dd>
                    </div>
                    <div>
                        <dt>Penumpang</dt>
                        <dd data-summary="passengers">1 orang</dd>
                    </div>
                </dl>
                <button type="submit" class="lp-btn lp-btn--primary" data-ticket-submit @disabled($locations->isEmpty())>Lihat jadwal</button>
            </div>
        </form>
    </div>
</section>
