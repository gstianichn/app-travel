<footer class="footer">
    <div class="container footer-content">

        <div>
            <h3>TravelKu</h3>
            <p>
                Platform pemesanan travel minibus dan van
                berbasis website.
            </p>
        </div>

        <div>
            <h4>Navigasi</h4>

            <a href="{{ url('/') }}">
                Beranda
            </a>

            <a href="{{ url('/search') }}">
                Cari Travel
            </a>
        </div>

        <div>
            <h4>Layanan</h4>

            <a href="#">
                Tentang Kami
            </a>

            <a href="#">
                Bantuan
            </a>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>
                &copy; {{ date('Y') }} TravelKu.
                Semua hak dilindungi.
            </p>
        </div>
    </div>
</footer>