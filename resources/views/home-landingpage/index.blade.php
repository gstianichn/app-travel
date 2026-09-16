@extends('layouts.app')

@section('title', 'TravelKu - Perjalanan Nyaman')

@section('content')
<section class="travel-hero">
    <div class="travel-hero-image"></div>
    <div class="container travel-hero-inner">
        <div class="travel-hero-copy">
            <span class="hero-kicker">TravelKu</span>
            <h1>Perjalanan Nyaman,<br><span>Sampai Tujuan.</span></h1>
            <p>Pesan tiket travel antar kota dengan mudah, cepat, dan nyaman.</p>
        </div>

        <div class="travel-search-card">
            <div class="search-card-heading">
                <div><span class="section-label">Cari Perjalanan</span><h2>Mau pergi ke mana?</h2></div>
                <span class="search-card-mark">01</span>
            </div>
            <form action="{{ route('search') }}" method="GET">
                <div class="search-form-grid">
                    <div class="form-group"><label for="origin">Dari</label><select name="origin" id="origin" class="form-control" required><option value="">Pilih kota asal</option>@foreach ($locations ?? [] as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label for="destination">Ke</label><select name="destination" id="destination" class="form-control" required><option value="">Pilih kota tujuan</option>@foreach ($locations ?? [] as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label for="departure_date">Tanggal</label><input type="date" name="departure_date" id="departure_date" class="form-control" min="{{ now()->toDateString() }}" required></div>
                    <div class="form-group"><label for="passengers">Penumpang</label><select name="passengers" id="passengers" class="form-control" required>@for ($passenger = 1; $passenger <= 10; $passenger++)<option value="{{ $passenger }}">{{ $passenger }} Penumpang</option>@endfor</select></div>
                </div>
                <button type="submit" class="btn btn-primary search-button">Cari Travel <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </div>
</section>

<section class="trust-strip"><div class="container trust-grid"><div><strong>✓</strong><span><b>Aman & Terpercaya</b><small>Driver terpilih dan berpengalaman</small></span></div><div><strong>₽</strong><span><b>Pembayaran Mudah</b><small>Berbagai metode pembayaran</small></span></div><div><strong>▣</strong><span><b>E-Ticket Instan</b><small>Tiket langsung di email Anda</small></span></div><div><strong>◉</strong><span><b>Customer Service</b><small>Siap membantu setiap hari</small></span></div></div></section>

<section class="section route-showcase"><div class="container"><div class="section-heading"><div><span class="section-label">Jelajahi Indonesia</span><h2>Rute Populer</h2></div><a href="{{ route('search') }}" class="section-link">Lihat Semua <span aria-hidden="true">→</span></a></div><div class="route-grid">
    @php
        $popularRoutes = [
            ['from' => 'Jakarta', 'to' => 'Bandung', 'price' => 'Rp 150.000', 'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=700&q=80'],
            ['from' => 'Jakarta', 'to' => 'Yogyakarta', 'price' => 'Rp 250.000', 'image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=700&q=80'],
            ['from' => 'Bandung', 'to' => 'Jakarta', 'price' => 'Rp 150.000', 'image' => 'https://images.unsplash.com/photo-1555899434-94d1368aa7af?auto=format&fit=crop&w=700&q=80'],
            ['from' => 'Jakarta', 'to' => 'Cianjur', 'price' => 'Rp 125.000', 'image' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=700&q=80'],
        ];
    @endphp
    @foreach ($popularRoutes as $route)<a href="{{ route('search') }}" class="route-photo-card"><img src="{{ $route['image'] }}" alt="Pemandangan perjalanan {{ $route['from'] }} ke {{ $route['to'] }}" loading="lazy"><div class="route-photo-overlay"><span>{{ $route['from'] }} → {{ $route['to'] }}</span><strong>{{ $route['price'] }}</strong></div></a>@endforeach
</div></div></section>

<section class="promo-section"><div class="container promo-banner"><div><span class="section-label">Promo Spesial</span><h2>Perjalanan nyaman,<br><em>harga bersahabat.</em></h2><a href="{{ route('search') }}" class="btn btn-light">Lihat Promo <span aria-hidden="true">→</span></a></div><img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=900&q=85" alt="Bus travel TravelKu" loading="lazy"></div></section>

<section class="section fleet-section"><div class="container"><div class="section-heading"><div><span class="section-label">Armada Kami</span><h2>Teman perjalanan terbaik</h2></div><a href="{{ route('search') }}" class="section-link">Lihat Semua <span aria-hidden="true">→</span></a></div><div class="fleet-grid"><article><img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=700&q=80" alt="Armada Hiace" loading="lazy"><h3>Hiace</h3><p>12 Kursi · AC · USB Charger</p></article><article><img src="https://images.unsplash.com/photo-1609521263047-f8f205293f24?auto=format&fit=crop&w=700&q=80" alt="Armada Isuzu Elf" loading="lazy"><h3>Isuzu Elf</h3><p>15 Kursi · AC · Bagasi</p></article><article><img src="https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=700&q=80" alt="Armada Medium Bus" loading="lazy"><h3>Medium Bus</h3><p>25 Kursi · AC · Reclining Seat</p></article></div></div></section>

<section class="review-section"><div class="container"><div class="section-heading"><div><span class="section-label">Cerita Mereka</span><h2>Rating & Review</h2></div><div class="rating-score"><b>4.7/5</b><span>★★★★★<small> dari 1.250 ulasan</small></span></div></div><div class="review-grid"><article><div class="review-stars">★★★★★</div><p>Perjalanannya nyaman dan drivernya ramah. Pasti pesan lagi.</p><strong>Andi Pratama</strong><small>Jakarta → Bandung</small></article><article><div class="review-stars">★★★★★</div><p>Booking sangat mudah, armadanya bersih, dan tepat waktu.</p><strong>Siti Rahayu</strong><small>Bandung → Yogyakarta</small></article><article><div class="review-stars">★★★★★</div><p>Customer service responsif. Pengalaman travel yang menyenangkan.</p><strong>Budi Santoso</strong><small>Surabaya → Malang</small></article></div></div></section>

<section class="faq-section section"><div class="container"><div class="section-heading centered"><div><span class="section-label">Punya Pertanyaan?</span><h2>FAQ</h2></div></div><div class="faq-grid"><details open><summary>Bagaimana cara memesan tiket?</summary><p>Pilih rute, tanggal, dan jumlah penumpang, lalu ikuti proses pemesanan.</p></details><details><summary>Apakah bisa melakukan perubahan jadwal?</summary><p>Hubungi customer service kami untuk bantuan perubahan jadwal.</p></details><details><summary>Bagaimana cara pembayaran?</summary><p>Kami menyediakan berbagai metode pembayaran digital yang praktis.</p></details><details><summary>Di mana saya menerima e-ticket?</summary><p>E-ticket dikirimkan setelah pembayaran berhasil dikonfirmasi.</p></details></div></div></section>
@endsection