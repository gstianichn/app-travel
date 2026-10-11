{{--
    Public footer (landing and search). $footerContact comes from the view composer in
    AppServiceProvider; contact lines only appear once the admin fills them in.
--}}
@php
    $hasContact = $footerContact['phone'] || $footerContact['whatsapp'] || $footerContact['email'] || $footerContact['address'];
@endphp

<footer class="ft">
    <div class="ft-road" aria-hidden="true"></div>

    <div class="container ft-top">
        <div class="ft-brand">
            <a href="{{ route('home') }}" class="ft-logo">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h13l4 4v7H3V6z" />
                    <circle cx="7.5" cy="17.5" r="2" />
                    <circle cx="16.5" cy="17.5" r="2" />
                    <path d="M16 6v4h4" />
                    <path d="M7 10h4" />
                </svg>
                TravelKu
            </a>
            <p>Travel minibus antar kota. Pilih rute, jam, dan kursi dari HP, bayar lewat virtual account, QRIS, atau e-wallet, lalu tunjukkan QR e-ticket saat naik.</p>
            <div class="ft-actions">
                <a href="{{ route('search') }}" class="lp-btn lp-btn--primary">Cari jadwal</a>
                @guest
                    <a href="{{ route('register') }}" class="ft-btn-outline">Daftar akun</a>
                @endguest
            </div>
        </div>

        <nav class="ft-col" aria-labelledby="ft-bantuan">
            <h2 id="ft-bantuan" class="ft-heading">Bantuan</h2>
            <ul class="ft-links">
                <li><a href="{{ route('home') }}#cara-pesan">Cara pesan tiket</a></li>
                <li><a href="{{ route('home') }}#faq">Pembatalan dan refund</a></li>
                <li><a href="{{ route('home') }}#faq">Kapan harus di pool</a></li>
                <li><a href="{{ route('home') }}#armada">Armada</a></li>
                @guest
                    <li><a href="{{ route('user.login') }}">Masuk</a></li>
                @else
                    <li><a href="{{ route('dashboard.user.index') }}">Akun saya</a></li>
                @endguest
            </ul>
        </nav>

        <div class="ft-col">
            @if ($hasContact)
                <h2 class="ft-heading">Hubungi kami</h2>
                <ul class="ft-links">
                    @if ($footerContact['whatsapp'])
                        <li><a href="{{ $footerContact['whatsapp'] }}" target="_blank" rel="noopener">WhatsApp</a></li>
                    @endif
                    @if ($footerContact['phone'])
                        <li><a href="{{ $footerContact['phone']['tel'] }}">{{ $footerContact['phone']['display'] }}</a></li>
                    @endif
                    @if ($footerContact['email'])
                        <li><a href="mailto:{{ $footerContact['email'] }}">{{ $footerContact['email'] }}</a></li>
                    @endif
                    @if ($footerContact['address'])
                        <li class="ft-address">{{ $footerContact['address'] }}</li>
                    @endif
                </ul>
            @endif

            <h2 class="ft-heading {{ $hasContact ? 'ft-heading--spaced' : '' }}">Pembayaran</h2>
            @include('components.payment-logos', ['class' => 'ft-pay-logos'])
            <p class="ft-note">Virtual account, QRIS, dan e-wallet. Diproses lewat Midtrans.</p>
        </div>
    </div>

    <div class="container ft-bottom">
        <p>&copy; {{ date('Y') }} TravelKu. Semua hak dilindungi.</p>
        <a href="#konten" class="ft-top-link">Kembali ke atas</a>
    </div>
</footer>
