# Test Case Aplikasi Travel Minibus/Van (per Role)

Dokumen ini untuk pengujian manual semua fitur, dikelompokkan per role. Setiap test case punya kolom **Hasil** yang diisi saat pengujian: `PASS`, `FAIL`, atau `BLOCKED`.

Test case bertanda ⚠️ mengarah ke bug yang sudah ditemukan lewat review kode (lihat [Bagian 2](#2-temuan-awal-bug-yang-sudah-terdeteksi)). Hasil yang diharapkan di test case itu adalah perilaku **yang seharusnya**, jadi kemungkinan besar hasilnya FAIL sampai bugnya diperbaiki.

---

## 1. Persiapan

### 1.1 Reset data

```bash
php artisan migrate:fresh --seed
npm run build        # atau: composer run dev
```

### 1.2 Akun uji (dari `DummyDataSeeder` & `TravelSeeder`)

| Role | Halaman login | Email | Password | Kode akses |
|---|---|---|---|---|
| Admin Utama (`admin_utama`) | `/login` | admin@travel.test | password | ADMIN123 |
| Staff Jakarta (`staff`) | `/login` | staff@travel.test | password | STF-2026-TRAVEL |
| Staff Bandung (`staff`) | `/login` | staff.bandung@travel.test | password | STF-BDO-2026 |
| Customer Service (`cs`) | `/login` | cs@travel.test | password | CS-2026-TRAVEL |
| Driver (`driver`) | `/login` | driver@travel.test | password | DRV-2026-TRAVEL |
| Driver lain | `/login` | driver.bambang@travel.test (dan driver.*@travel.test lainnya) | password | DRV-2026-TRAVEL |
| Penumpang (`user`) | `/user/login` | user@travel.test | password | (tidak ada) |
| Penumpang lain | `/user/login` | budi@, dewi@, rizky@, anisa@travel.test | password | (tidak ada) |

### 1.3 Data uji yang sering dipakai

| Data | Nilai |
|---|---|
| Kode promo checkout | `HEMATLIBURAN` (25%, maks Rp50.000, min Rp100.000, khusus Bandung–Jakarta), `TRAVELHEMAT` (Rp20.000, min Rp100.000) |
| Kode klaim voucher | `HEMATLIBURAN`, `TRAVELHEMAT`, `KOMPENSASI50`, `WELCOME2026`, `SUPERDISCOUNT` |
| NIK valid | 16 digit angka, mis. `3273010198000001` |
| Kanal bayar | `bca, mandiri, bni, bri, bsi, seabank, qris, gopay, shopeepay` |

### 1.4 Hasil test otomatis saat dokumen ini dibuat (15 Okt 2026)

`php artisan test --compact` → **186 test: 184 lulus, 1 gagal, 1 dilewati.**

- **Gagal:** `UserBookingTransactionFlowTest` › *flow a: user can view active tickets and simulate pool check-in*. Jadwal di test di-hardcode "hari ini jam 14:30". Kalau test dijalankan setelah 14:30, tiket pindah ke tab Riwayat sehingga nomor tiket tidak tampil. Test ini flaky karena bergantung pada jam (BUG-22).
- **Dilewati:** `PengaturanTest` › *customer user cannot access pengaturan* (`->skip('role middleware returns 403')`).

---

## 2. Temuan awal (bug yang sudah terdeteksi)

Sumber: review kode controller/route dan pengecekan langsung. "Terkonfirmasi" artinya sudah dibuktikan dengan menjalankan kode; yang lain berasal dari membaca kode dan perlu dibuktikan lewat test case terkait.

| ID | Prioritas | Masalah | Lokasi | Test case |
|---|---|---|---|---|
| BUG-01 | Kritis | Webhook pembayaran menerima request **tanpa `signature_key`** (verifikasi hanya jalan kalau field itu dikirim). Jika `transaction_status` kosong, nilai default-nya `settlement`. Siapa pun yang tahu `order_id` bisa menandai pesanan lunas dengan `POST /api/payment/webhook {"order_id":"..."}`. | `UserDashboardController@paymentWebhook` | PAY-05, PAY-06 |
| BUG-02 | Kritis | Akun yang dinonaktifkan/diblokir (`status = inactive`) **tetap bisa login**. Status tidak dicek di login penumpang, login petugas, maupun login Google. | `Auth/LoginController`, `Auth/UserLoginController`, `Auth/GoogleLoginController` | AUTH-09, AUTH-16, ADM-USR-07, ADM-STF-06 |
| BUG-03 | Kritis (terkonfirmasi) | Kode akses staf dari form Kelola Staff disimpan **plaintext**, padahal login memakai `Hash::check`. Login staf baru itu menghasilkan **error 500** (`RuntimeException: This password does not use the Bcrypt algorithm`). Staf yang dibuat tanpa kode akses tidak bisa login sama sekali. | `AdminDashboardController@storeStaff/updateStaff` | ADM-STF-02, ADM-STF-03 |
| BUG-04 | Kritis | Driver yang dibuat lewat Kelola Driver tidak diberi kode akses, jadi **tidak bisa login**. | `AdminDashboardController@storeDriver` | ADM-DRV-02 |
| BUG-05 | Kritis | Seat map dan proses kunci kursi hanya menganggap status `paid/confirmed/success/lunas` sebagai terisi. Kursi milik booking `checked_in` (penumpang yang sudah check-in mandiri) **tampil kosong dan bisa dipesan orang lain** sehingga terjadi double booking. Di SQLite, booking manual berstatus `Paid`/`Pending` (huruf kapital) juga lolos dari pengecekan. | `UserDashboardController@lockSeats`, `buildSeatOccupancyMap` | USR-BK-09, X-03 |
| BUG-06 | Kritis | Endpoint aksi driver tidak mengecek kepemilikan jadwal. Driver A bisa mengubah status trip, GPS, laporan, dan SOS **jadwal driver lain**, serta check-in penumpang mana pun. Dengan `mark_paid=1`, driver bisa membuat booking belum bayar menjadi `confirmed`. | `DriverDashboardController@updateTripStatus/updateGps/updateCheckin/reportIncident/triggerSos` | DRV-12, DRV-13 |
| BUG-07 | Tinggi | Voucher dompet (`KOMPENSASI50`, `WELCOME2026`, `SUPERDISCOUNT`) lolos validasi promo tapi **diskonnya tidak diterapkan** saat data disimpan. `HEMATLIBURAN` diterapkan tanpa cek rute. Voucher **tidak pernah ditandai terpakai** sehingga bisa dipakai berulang, dan voucher kedaluwarsa bisa diklaim ulang tanpa batas. | `savePassengerData`, `claimVoucher` | USR-BK-14, USR-BK-15, USR-PR-05 |
| BUG-08 | Tinggi | Check-in mandiri penumpang tidak mengecek status. Booking `pending` (belum bayar) atau tiket `cancelled` tetap bisa di-check-in, dan booking pending bisa langsung dibuatkan tiket. | `checkInTicket` | USR-TK-04, USR-TK-05 |
| BUG-09 | Tinggi | Refund dari penumpang berstatus `Diajukan`, tapi admin dan CS hanya menghitung `Menunggu/Pending`. Akibatnya: (a) counter refund pending tidak bertambah, (b) admin bisa membuat refund **ganda** untuk booking yang sama, (c) kalau admin **menolak** refund penumpang, booking tetap batal dan kursi sudah dilepas, padahal pesan sistem bilang "tiket tetap aktif". | `submitRefund`, `refundIndex`, `storeRefund`, `rejectRefund`, `CustomerServiceDashboardController@index` | ADM-RF-03, ADM-RF-07, CS-03 |
| BUG-10 | Tinggi | Approve pembayaran manual tidak mengecek status booking atau bentrok kursi, sehingga booking yang sudah batal/kedaluwarsa bisa hidup lagi walau kursinya sudah dipakai orang lain. Reject pada pembayaran yang sudah lunas ikut membatalkan booking lunas. | `verifyPayment` | ADM-PAY-05, ADM-PAY-06 |
| BUG-11 | Tinggi | Sinkron gateway dengan status `expire` mengubah booking menjadi `Expired`, tapi perhitungan sisa kursi hanya mengecualikan `cancelled`, jadi **kursi tidak pernah dilepas**. | `syncGatewayStatus`, `Schedule::recalculateAvailableSeats` | ADM-PAY-09 |
| BUG-12 | Tinggi | Menghapus kendaraan yang punya jadwal lampau atau selesai menghasilkan **error 500** (foreign key `restrictOnDelete` di `schedules.vehicle_id` dan `booking_passengers.seat_id`). | `destroyVehicle` | ADM-VH-07 |
| BUG-13 | Tinggi | Mengganti armada pada jadwal yang sudah punya penumpang membuat kursi penumpang tetap menunjuk kursi armada lama. Tidak ada cek bentrok armada/sopir seperti di "Tambah Jadwal", begitu juga di "Generate Jadwal Massal". | `assignScheduleVehicleDriver`, `batchGenerateSchedules` | ADM-SC-06b, ADM-SC-08 |
| BUG-14 | Sedang | Mengubah status jadwal ke `Cancelled` tidak membatalkan booking, tidak membuat refund, dan tidak memberi notifikasi ke penumpang. | `updateScheduleStatus` | ADM-SC-10 |
| BUG-15 | Sedang | Reschedule tidak mengecek bahwa kursi baru milik armada jadwal baru, mengizinkan jadwal tujuan yang sudah lewat/dibatalkan, tidak menghitung selisih harga, dan `PUT` tetap jalan untuk booking yang sudah batal (yang diblokir hanya form-nya). | `updateRescheduleBooking` | ADM-BK-10, ADM-BK-11 |
| BUG-16 | Sedang | Booking manual memakai harga rute, bukan harga jadwal/akhir pekan seperti di online. Tidak ada cek bahwa kursi milik armada jadwal. Pencarian pelanggan berdasarkan nomor HP tidak memfilter role, sehingga booking bisa menempel ke akun staf/driver. | `storeManualBooking` | ADM-BK-05, ADM-BK-06 |
| BUG-17 | Sedang | Validasi tiket oleh staf: penumpang yang sudah check-in mandiri (`checked_in`) mendapat pesan **"Tiket belum lunas"**. Hak akses staf (`permissions`) tidak pernah ditegakkan. Update boarding dengan ID penumpang yang tidak ada tetap `success: true`. Kode demo `TKV-…` mengembalikan hasil palsu. | `AdminBiasaDashboardController` | STF-VT-08, STF-VT-12, STF-VT-13, STF-05 |
| BUG-18 | Sedang | Driver menekan "Selesai" lalu semua booking non-batal di jadwal itu, **termasuk yang belum bayar**, menjadi `completed` dan dihitung lunas. Urutan status trip juga tidak dipaksa (bisa langsung `completed`). | `updateTripStatus` | DRV-08, DRV-09 |
| BUG-19 | Sedang | `save-passenger` dan `/api/payment/charge` pada booking yang sudah lunas me-reset data pembayaran ke `pending`. | `savePassengerData`, `apiChargePayment` | USR-BK-18 |
| BUG-20 | Sedang | Pemilihan kursi/lock kursi lewat URL langsung tidak mengecek jadwal batal atau sudah berangkat. | `selectSeat`, `lockSeats` | USR-BK-10 |
| BUG-21 | Sedang | Menghapus akun pelanggan ikut menghapus semua booking, refund, dan pembayarannya (cascade), sehingga laporan keuangan berubah. | `destroyUser` + migrasi | ADM-USR-09 |
| BUG-22 | Rendah | Test otomatis flaky (lihat 1.4). `ensureUserPresets()` hanya jalan saat unit test, jadi perilaku saat test berbeda dengan aplikasi asli. | `UserBookingTransactionFlowTest`, `ensureUserPresets` | — |
| BUG-23 | Rendah | Ulasan bisa dikirim untuk booking yang belum jalan, belum bayar, atau batal. | `submitReview` | USR-TK-12 |
| BUG-24 | Rendah | `UserSeeder` (role `admin`) tidak dipanggil `DatabaseSeeder`, dan role `admin`/`customer_service` diizinkan route tapi tidak bisa dipilih di form login. | `LoginController`, seeder | AUTH-14 |

---

## 3. Autentikasi (semua role)

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| AUTH-01 | Login admin utama valid | `/login`, role Admin Utama, admin@travel.test / password / ADMIN123 | Masuk ke `/dashboard/admin` | |
| AUTH-02 | Login staff valid | role Staff, staff@travel.test / password / STF-2026-TRAVEL | Masuk ke `/admin/dashboard` | |
| AUTH-03 | Login CS valid | role CS, cs@travel.test / password / CS-2026-TRAVEL | Masuk ke `/customer-service/dashboard` | |
| AUTH-04 | Login driver valid | role Driver, driver@travel.test / password / DRV-2026-TRAVEL | Masuk ke `/driver/dashboard` | |
| AUTH-05 | Password salah | Password `salah123` | Tetap di `/login`, error "Email atau password salah." | |
| AUTH-06 | Role tidak sesuai akun | staff@travel.test tapi pilih role Driver | Error "Role yang dipilih tidak sesuai dengan akun Anda." | |
| AUTH-07 | Kode akses salah | Kode akses `XXX` | Error "Kode akses tidak valid." | |
| AUTH-08 | Field kosong | Submit form kosong | Pesan wajib isi untuk email, password, role, kode akses | |
| AUTH-09 ⚠️ | Petugas nonaktif login | Nonaktifkan staff (ADM-STF-06), lalu login | Ditolak dengan pesan akun nonaktif (BUG-02) | |
| AUTH-10 | Login penumpang valid | `/user/login`, user@travel.test / password | Masuk ke `/user/dashboard` | |
| AUTH-11 | Petugas login di form penumpang | `/user/login` dengan admin@travel.test | Ditolak "Email atau password salah." | |
| AUTH-12 | Redirect setelah login | Buka `/user/login?redirect=<url hasil cari>` lalu login | Kembali ke URL hasil pencarian | |
| AUTH-13 | Redirect ke domain luar | `/user/login?redirect=https://evil.com` | Redirect diabaikan, masuk ke `/user/dashboard` | |
| AUTH-14 ⚠️ | Role `admin`/`customer_service` | Buat user role `admin`, coba login | Bisa login atau memang tidak didukung; perlu keputusan (BUG-24) | |
| AUTH-15 | Registrasi penumpang | `/register`: nama, email baru, HP, password 8+ karakter + konfirmasi | Akun role `user` dibuat dan langsung login | |
| AUTH-16 ⚠️ | Penumpang diblokir login | Blokir user (ADM-USR-07), login di `/user/login` | Ditolak (BUG-02) | |
| AUTH-17 | Registrasi email duplikat | Email user@travel.test | Error email sudah dipakai | |
| AUTH-18 | Registrasi password pendek / tidak cocok | Password `1234567`, atau konfirmasi berbeda | Error validasi min 8 / konfirmasi | |
| AUTH-19 | Lupa password | `/forgot-password`, email terdaftar | Pesan link reset terkirim (cek mail log) | |
| AUTH-20 | Lupa password, email tidak terdaftar | email acak | Pesan error dari Laravel | |
| AUTH-21 | Reset password | Buka link dari email, isi password baru | Password berubah, bisa login dengan password baru | |
| AUTH-22 | Login Google belum dikonfigurasi | `GOOGLE_CLIENT_ID` kosong, klik "Masuk dengan Google" | Kembali ke `/user/login` dengan pesan "belum bisa dipakai" | |
| AUTH-23 | Login Google akun baru | Google OAuth dengan email baru | Akun role `user` dibuat, email terverifikasi, masuk dashboard | |
| AUTH-24 | Login Google pakai email petugas | Email Google = cs@travel.test | Ditolak: "terdaftar sebagai akun petugas" | |
| AUTH-25 | Google meng-klaim booking tamu | Pesan sebagai tamu dengan email X, lalu login Google dengan email X | Booking tamu pindah ke akun | |
| AUTH-26 | Logout petugas | Klik Keluar di dashboard petugas | Sesi berakhir, kembali ke `/login` | |
| AUTH-27 | Logout penumpang | Klik Keluar di dashboard penumpang | Kembali ke `/user/login` | |
| AUTH-28 | Halaman login saat sudah login | Login, lalu buka `/login` atau `/register` | Diarahkan keluar dari halaman tamu | |

---

## 4. Kontrol akses antar role

Ulangi untuk setiap baris: login dengan role di kolom kiri, buka URL contoh dari tiap area.

| ID | Pengakses | `/dashboard/admin` | `/admin/dashboard` | `/driver/dashboard` | `/customer-service/dashboard` | `/user/dashboard` | Hasil |
|---|---|---|---|---|---|---|---|
| ACL-01 | Tamu (belum login) | → `/login` | → `/login` | → `/login` | → `/login` | → `/user/login` | |
| ACL-02 | Admin Utama | 200 | 403 | 403 | 403 | 403 | |
| ACL-03 | Staff | 403 | 200 | 403 | 403 | 403 | |
| ACL-04 | Driver | 403 | 403 | 200 | 403 | 403 | |
| ACL-05 | CS | 403 | 403 | 403 | 200 | 403 | |
| ACL-06 | Penumpang | 403 | 403 | 403 | 403 | 200 | |

| ID | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ACL-07 | Aksi POST/PATCH lintas role | Sebagai staff, kirim `PATCH /refund/{id}/approve` | 403 | |
| ACL-08 | Penumpang buka booking orang lain | Login dewi@, buka `/user/booking/pembayaran/{id booking milik user@}` | 403 | |
| ACL-09 | Tamu buka booking tamu lain | Browser A buat booking tamu, browser B (sesi lain) buka URL-nya | 403 | |
| ACL-10 | Penumpang ubah penumpang tersimpan milik orang lain | `PUT /user/penumpang-tersimpan/{id milik user lain}` | 403 | |
| ACL-11 | Penumpang refund/ulasan booking orang lain | `POST /user/tiket/{booking lain}/refund` | 403 | |
| ACL-12 | Link e-tiket tanpa signature | Buka `/e-tiket/{id}` tanpa query signature | 403 (invalid signature) | |
| ACL-13 | Link e-tiket booking belum lunas | URL bertanda tangan untuk booking pending | 404 | |

---

## 5. Publik / Tamu (tanpa login)

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| GST-01 | Landing page | Buka `/` | Halaman tampil: rute, armada, ulasan, tanpa error | |
| GST-02 | Cari jadwal valid | `/search`, asal ≠ tujuan, tanggal hari ini/besok, 1 penumpang | Daftar jadwal sesuai rute, terurut jam | |
| GST-03 | Cari asal = tujuan | Asal dan tujuan sama | Error validasi `different` | |
| GST-04 | Cari tanggal lampau | Tanggal kemarin | Error validasi tanggal | |
| GST-05 | Jumlah penumpang di luar batas | 0 atau 11 | Error validasi (1–10) | |
| GST-06 | Jadwal hari ini yang sudah lewat | Cari hari ini setelah jam salah satu jadwal | Jadwal yang sudah lewat tidak muncul | |
| GST-07 | Rute tidak terhubung langsung | Pilih kota yang hanya terhubung via transit | Info kota transit tampil, tanpa error | |
| GST-08 | Armada maintenance | Ubah kendaraan jadwal ke `maintenance`, cari lagi | Jadwal tersebut tidak tampil | |
| GST-09 | Checkout tamu end-to-end | Pilih kursi → isi data pemesan & penumpang → bayar → simulasi bayar | Booking `confirmed`, e-tiket & QR tampil | |
| GST-10 | Tamu lalu login | Mulai checkout tamu, lalu login/registrasi | Booking otomatis jadi milik akun | |

---

## 6. Penumpang (role `user`)

Login: user@travel.test / password.

### 6.1 Dashboard & pencarian

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| USR-01 | Dashboard | Buka `/user/dashboard` | Ringkasan tampil tanpa error | |
| USR-02 | Menu alias | Buka `/booking/dashboard`, `/history/dashboard`, `/refund/dashboard` | Semua 200 | |
| USR-03 | Cari jadwal dari dashboard | `/user/cari-jadwal` dengan data valid | Hasil sama dengan GST-02 | |

### 6.2 Booking & pembayaran

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| USR-BK-01 | Pilih kursi | Klik jadwal, pilih kursi sesuai jumlah penumpang | Kursi terkunci, masuk halaman data penumpang dengan timer 15 menit | |
| USR-BK-02 | Jumlah kursi ≠ jumlah penumpang | Penumpang 2, pilih 1 kursi | Error validasi `size` | |
| USR-BK-03 | Kursi dari armada lain | Kirim `seat_ids` milik kendaraan lain | "Kursi yang dipilih tidak valid…" | |
| USR-BK-04 | Kursi bentrok | Browser A mengunci kursi 1A, browser B (akun lain) memilih 1A | B ditolak: "baru saja dikunci atau dipesan…" | |
| USR-BK-05 | Pilih ulang kursi | Kunci kursi lalu kembali pilih kursi lain di jadwal yang sama | Booking pending lama terhapus, hanya 1 booking pending | |
| USR-BK-06 | Kunci kursi habis | Tunggu > 15 menit di halaman data penumpang, refresh | Booking batal, kembali ke pencarian dengan pesan waktu habis | |
| USR-BK-07 | Kursi lepas setelah kedaluwarsa | Setelah USR-BK-06, akun lain pilih kursi yang sama | Berhasil | |
| USR-BK-08 | Seat map kursi lunas | Buka seat map jadwal yang ada booking lunas | Kursi lunas tampil terisi, tidak bisa dipilih | |
| USR-BK-09 ⚠️ | Kursi penumpang yang sudah check-in | Booking A lunas lalu check-in mandiri (USR-TK-03); akun B buka seat map jadwal yang sama | Kursi A tetap terisi dan tidak bisa dikunci (BUG-05) | |
| USR-BK-10 ⚠️ | Jadwal batal/lewat lewat URL langsung | Admin set jadwal `Cancelled`, lalu buka `/user/booking/pilih-kursi/{id}` dan kunci kursi | Ditolak (BUG-20) | |
| USR-BK-11 | Validasi data pemesan | HP `abc`, email tidak valid, nama kosong | Error validasi; HP harus angka/+/-, min 8 | |
| USR-BK-12 | Promo HEMATLIBURAN | Rute Bandung–Jakarta, subtotal ≥ 100.000 | Diskon 25%, maks Rp50.000 | |
| USR-BK-13 | HEMATLIBURAN rute lain / di bawah minimum | Rute non Bandung–Jakarta, atau subtotal < 100.000 | Validasi promo menolak dengan pesan yang sesuai | |
| USR-BK-14 ⚠️ | Voucher dompet diterapkan | Klaim `KOMPENSASI50`, pakai di checkout (subtotal ≥ 50.000), simpan | Total di halaman pembayaran berkurang Rp50.000 (BUG-07) | |
| USR-BK-15 ⚠️ | Voucher sekali pakai | Bayar dengan voucher (USR-BK-14), lalu pakai kode yang sama di booking baru | Ditolak "sudah pernah digunakan" (BUG-07) | |
| USR-BK-16 | Kode promo tidak dikenal | `ASAL123` | "Kode voucer … tidak ditemukan" | |
| USR-BK-17 | Ganti metode bayar | Di halaman pembayaran, pindah BCA → QRIS → GoPay | Instruksi bayar berganti sesuai kanal | |
| USR-BK-18 ⚠️ | Simpan ulang data booking lunas | Setelah lunas, kirim ulang `POST save-passenger/{id}` | Ditolak/redirect ke sukses; status pembayaran tetap lunas (BUG-19) | |
| USR-BK-19 | Simulasi bayar (non-production) | Klik tombol simulasi bayar | Booking `confirmed`, tiket terbit, email tiket terkirim 1x | |
| USR-BK-20 | Simulasi bayar di production | `APP_ENV=production`, panggil simulate-payment | 404 | |
| USR-BK-21 | Polling status | Buka `/user/booking/pembayaran/{id}/status` | JSON `is_paid` sesuai status | |
| USR-BK-22 | Halaman sukses booking belum lunas | Buka `/user/booking/sukses/{id}` saat pending | Redirect ke halaman pembayaran | |

### 6.3 Tiket saya, check-in, refund, ulasan

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| USR-TK-01 | Daftar tiket aktif | Buka `/tiket/dashboard` | Tiket lunas yang akan berangkat tampil dengan QR | |
| USR-TK-02 | Riwayat otomatis | Tiket dengan keberangkatan > 1 jam lalu | Pindah ke tab Riwayat, status `completed` | |
| USR-TK-03 | Check-in mandiri | Klik check-in pada tiket aktif | Tiket `Boarded`, semua penumpang `boarded` | |
| USR-TK-04 ⚠️ | Check-in tiket yang dibatalkan | Batalkan booking (refund), lalu kirim `POST /user/tiket/{ticket}/check-in` | Ditolak (BUG-08) | |
| USR-TK-05 ⚠️ | Check-in booking belum bayar | Kirim check-in dengan ID booking pending | Ditolak, tidak ada tiket terbit (BUG-08) | |
| USR-TK-06 | Ajukan refund ≥ 1 jam sebelum berangkat | Isi alasan, bank, no rekening, pemilik | Refund 75% (potongan 25%), booking batal, kursi dilepas | |
| USR-TK-07 | Refund < 1 jam sebelum berangkat | Jadwal 30 menit lagi | Ditolak: batas waktu pembatalan berakhir | |
| USR-TK-08 | Refund booking belum bayar / sudah batal | Booking pending atau cancelled | Ditolak | |
| USR-TK-09 | Refund field kosong | Kosongkan bank/rekening | Error validasi | |
| USR-TK-10 | Kirim ulasan | Nilai 1–5 untuk 3 aspek + komentar | Ulasan tersimpan, rata-rata dihitung | |
| USR-TK-11 | Ulasan nilai di luar batas | Nilai 0 atau 6, komentar > 1000 karakter | Error validasi | |
| USR-TK-12 ⚠️ | Ulasan sebelum perjalanan | Kirim ulasan untuk booking pending/akan datang | Ditolak (BUG-23) | |

### 6.4 Promo, notifikasi, akun

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| USR-PR-01 | Lihat dompet voucher | `/promo/dashboard`, filter all/active/used/expired | Daftar dan jumlah per status benar | |
| USR-PR-02 | Klaim voucher valid | Kode `WELCOME2026` | Voucher masuk dompet, aktif 30 hari | |
| USR-PR-03 | Klaim voucher yang masih aktif | Klaim `WELCOME2026` lagi | Ditolak: "sudah ada … masih aktif" | |
| USR-PR-04 | Kode tidak valid | `ABCDEF` | "tidak valid atau promo sudah berakhir" | |
| USR-PR-05 ⚠️ | Klaim ulang voucher kedaluwarsa/terpakai | Ubah `expires_at` voucher ke kemarin, klaim lagi | Seharusnya ditolak (BUG-07) | |
| USR-NT-01 | Daftar notifikasi | `/notifikasi/dashboard`, filter transaksi/pengingat/operasional | Daftar dan jumlah per kategori benar | |
| USR-NT-02 | Buka notifikasi | Klik notifikasi | Ditandai dibaca, diarahkan ke deep link | |
| USR-NT-03 | Tandai semua dibaca | Klik "tandai semua" | Jumlah belum dibaca jadi 0 | |
| USR-NT-04 | Hapus notifikasi | Hapus satu notifikasi | Hilang dari daftar | |
| USR-NT-05 | Buka notifikasi milik user lain | `/user/notifikasi/{id milik user lain}/open` | 404 | |
| USR-NT-06 | Simulasi notifikasi | Kirim tipe pending, confirmed, expired, reminder_2h, operational_vehicle, operational_delay | Masing-masing muncul dengan isi sesuai | |
| USR-AK-01 | Ubah profil | Nama, email baru, HP | Tersimpan | |
| USR-AK-02 | Email sudah dipakai | Email budi@travel.test | Error unique | |
| USR-AK-03 | Ganti password | Password lama benar, baru 8+ dan konfirmasi | Berhasil, login dengan password baru | |
| USR-AK-04 | Password lama salah | Password lama salah | "Kata sandi saat ini … salah" | |
| USR-AK-05 | Tambah penumpang tersimpan | Nama + NIK 16 digit | Tersimpan | |
| USR-AK-06 | NIK tidak valid | NIK 15 digit atau ada huruf | Error NIK 16 digit | |
| USR-AK-07 | Penumpang utama | Tandai penumpang B utama | Hanya B yang `is_primary` | |
| USR-AK-08 | Edit / hapus penumpang tersimpan | Edit nama lalu hapus | Berhasil | |
| USR-AK-09 | Quick passenger di checkout | Checkout setelah ada penumpang tersimpan | Penumpang tersimpan muncul sebagai pilihan | |

---

## 7. Admin Utama (role `admin_utama`)

Login: admin@travel.test / password / ADMIN123.

### 7.1 Dashboard

| ID | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-01 | Dashboard admin | Buka `/dashboard/admin` | Statistik tampil tanpa error | |

### 7.2 Kelola User (pelanggan)

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-USR-01 | Daftar & cari user | `/kelola-user`, cari nama/email | Hanya role `user`, pencarian jalan | |
| ADM-USR-02 | Tambah user | Nama, email baru, password 8+ + konfirmasi | User role `user` aktif dibuat | |
| ADM-USR-03 | Tambah user email duplikat / password tidak cocok | | Error validasi berbahasa Indonesia | |
| ADM-USR-04 | Detail user | Klik user | Profil & riwayat perjalanan tampil | |
| ADM-USR-05 | Detail/edit akun petugas lewat URL user | `/kelola-user/{id staff}` | 404 | |
| ADM-USR-06 | Edit user | Ubah nama/HP | Tersimpan | |
| ADM-USR-07 ⚠️ | Blokir user | Toggle status lalu user mencoba login | Status `inactive` dan login ditolak (BUG-02) | |
| ADM-USR-08 | Reset password user | Password baru + konfirmasi | User bisa login dengan password baru | |
| ADM-USR-09 ⚠️ | Hapus user yang punya transaksi | Hapus user dengan booking lunas, lalu buka laporan | Seharusnya dicegah atau data transaksi tetap ada (BUG-21) | |

### 7.3 Kelola Staff

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-STF-01 | Tambah staff | Nama, email, HP, cabang, password + konfirmasi, hak akses | Staff aktif dibuat | |
| ADM-STF-02 ⚠️ | Staff baru login (dengan kode akses) | Buat staff dengan kode akses `STF-NEW-01`, login di `/login` | Login berhasil. Saat ini: **error 500** (BUG-03) | |
| ADM-STF-03 ⚠️ | Staff baru tanpa kode akses | Buat staff tanpa kode akses, login | Ada cara login / kode digenerate. Saat ini tidak bisa login (BUG-03) | |
| ADM-STF-04 | Validasi tambah staff | Kosongkan cabang/HP, email duplikat | Error validasi | |
| ADM-STF-05 | Edit staff & hak akses | Ubah cabang, hapus hak `checkin_ticket` | Tersimpan | |
| ADM-STF-06 ⚠️ | Nonaktifkan staff | Toggle status lalu staff login | Login ditolak (BUG-02) | |
| ADM-STF-07 | Reset password staff | | Staff login dengan password baru | |
| ADM-STF-08 | Hapus staff | Hapus staff lain | Terhapus | |
| ADM-STF-09 | URL staff untuk akun non-staff | `/kelola-staff/{id user}` | 404 | |

### 7.4 Kelola Driver

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-DRV-01 | Tambah driver | Data lengkap + SIM unik + foto ≤ 2MB | Driver & akun user role `driver` dibuat | |
| ADM-DRV-02 ⚠️ | Driver baru login | Login driver baru di `/login` | Bisa login. Saat ini tidak punya kode akses (BUG-04) | |
| ADM-DRV-03 | SIM duplikat / foto > 2MB / bukan gambar | | Error validasi | |
| ADM-DRV-04 | Edit driver | Ubah SIM, status | Tersimpan | |
| ADM-DRV-05 | Ubah status cepat | standby → on_duty → on_leave | Tersimpan | |
| ADM-DRV-06 | Hapus driver yang punya jadwal mendatang | | Seharusnya dicegah. Saat ini jadwal kehilangan sopir tanpa peringatan | |

### 7.5 Memantau Kendaraan

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-VH-01 | Daftar & peringatan KIR/STNK | Kendaraan dengan KIR/STNK hampir habis | Peringatan legalitas tampil | |
| ADM-VH-02 | Tambah kendaraan | Plat unik, kapasitas 4–60, tahun 1990–tahun depan | Dibuat, kursi otomatis dibuat | |
| ADM-VH-03 | Validasi kendaraan | Plat duplikat, kapasitas 3 atau 61, tahun 1989 | Error validasi | |
| ADM-VH-04 | Edit kendaraan | Ubah fasilitas & foto | Tersimpan | |
| ADM-VH-05 | Ubah status | siap_jalan → maintenance | Jadwal armada ini hilang dari pencarian (GST-08) | |
| ADM-VH-06 | Hapus kendaraan dengan jadwal mendatang | | Ditolak dengan pesan | |
| ADM-VH-07 ⚠️ | Hapus kendaraan yang hanya punya jadwal lampau/selesai | | Ditolak dengan pesan rapi. Saat ini **error 500** (BUG-12) | |
| ADM-VH-08 | Hapus kendaraan tanpa jadwal | | Terhapus beserta kursinya | |

### 7.6 Pemesanan (booking)

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-BK-01 | Daftar & filter booking | `/pemesanan`, filter status/tanggal | Data & statistik benar | |
| ADM-BK-02 | Booking manual lunas | Pilih jadwal & kursi, nama, HP, sumber walk_in, Cash, paid | Booking `Paid`, pembayaran & tiket terbit | |
| ADM-BK-03 | Booking manual pending | payment_status pending | Booking `Pending`, tidak ada tiket | |
| ADM-BK-04 | Booking manual kursi terisi | Pilih kursi yang sudah dipesan | Ditolak dengan pesan | |
| ADM-BK-05 ⚠️ | Harga booking manual | Jadwal dengan harga khusus/akhir pekan | Total = harga jadwal, sama dengan online (BUG-16) | |
| ADM-BK-06 ⚠️ | HP pelanggan = HP staf | Isi HP milik akun staf | Booking tidak menempel ke akun staf (BUG-16) | |
| ADM-BK-07 | Seat map & manifes | `/pemesanan/jadwal/{id}/seat-map` dan `/manifes` | Tampil sesuai booking | |
| ADM-BK-08 | Detail booking | `/pemesanan/{id}` | Detail & e-tiket tampil | |
| ADM-BK-09 | Reschedule valid | Pilih jadwal baru, kursi sejumlah penumpang, alasan | Jadwal & kursi pindah, sisa kursi kedua jadwal dihitung ulang | |
| ADM-BK-10 | Reschedule jumlah kursi salah / kursi terisi | | Ditolak dengan pesan | |
| ADM-BK-11 ⚠️ | Reschedule ke jadwal lampau/batal atau kursi armada lain | | Ditolak (BUG-15) | |
| ADM-BK-12 | Reschedule booking batal | Buka form reschedule booking `cancelled` | Ditolak dengan pesan | |
| ADM-BK-13 | Batalkan booking | Klik batal | Booking & tiket `Cancelled`, kursi lepas | |
| ADM-BK-14 | Batalkan ulang | Batalkan booking yang sudah batal | "sudah dalam status dibatalkan" | |

### 7.7 Pembayaran

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-PAY-01 | Daftar & filter pembayaran | `/pembayaran` | Data & metrik omzet tampil | |
| ADM-PAY-02 | Detail pembayaran | `/pembayaran/{id}` | Tampil | |
| ADM-PAY-03 | Approve pembayaran pending | action approve | Payment `Success`, booking `Paid`, tiket terbit | |
| ADM-PAY-04 | Reject tanpa alasan | action reject, alasan kosong | Error `rejection_reason` wajib | |
| ADM-PAY-05 ⚠️ | Approve pembayaran booking yang sudah batal | Booking batal & kursinya sudah dibeli orang lain | Ditolak (BUG-10) | |
| ADM-PAY-06 ⚠️ | Reject pembayaran yang sudah lunas | | Ditolak (BUG-10) | |
| ADM-PAY-07 | Upload bukti transfer | jpg/png/webp/pdf ≤ 5MB | Tersimpan | |
| ADM-PAY-08 | Upload file tidak valid | .exe atau > 5MB | Error validasi | |
| ADM-PAY-09 ⚠️ | Sinkron gateway `expire` | Pilih status expire | Booking batal, sisa kursi kembali (BUG-11) | |
| ADM-PAY-10 | Sinkron gateway `settlement` | | Booking lunas, tiket terbit | |

### 7.8 Rute & Jadwal

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-RT-01 | Tambah rute | Asal ≠ tujuan, harga ≥ 10.000, durasi | Rute dibuat | |
| ADM-RT-02 | Rute duplikat / harga < 10.000 / asal = tujuan | | Ditolak | |
| ADM-RT-03 | Edit rute | | Tersimpan | |
| ADM-RT-04 | Nonaktifkan rute | Toggle status | Rute tidak muncul di pencarian | |
| ADM-RT-05 | Hapus rute yang punya tiket aktif | | Ditolak, disarankan menonaktifkan | |
| ADM-RT-06 | Tambah lokasi | Nama, kode unik ≤ 10 karakter, tipe pool/shuttle/drop_point | Dibuat | |
| ADM-SC-01 | Tambah jadwal | Rute, armada, sopir, tanggal ≥ hari ini, jam `HH:MM` | Dibuat | |
| ADM-SC-02 | Jadwal tanggal lampau / format jam salah | | Error validasi | |
| ADM-SC-03 | Armada bentrok | Armada sama, tanggal & jam sama | Ditolak dengan pesan | |
| ADM-SC-04 | Sopir bentrok | Sopir sama, tanggal & jam sama | Ditolak dengan pesan | |
| ADM-SC-05 | Generate jadwal massal | Rentang 7 hari, 3 jam keberangkatan | 21 jadwal dibuat. Jalankan ulang: 0 dibuat, 21 dilewati | |
| ADM-SC-06 | Generate dengan tanggal akhir < awal | | Error validasi | |
| ADM-SC-06b ⚠️ | Generate massal bentrok armada/sopir di rute lain | Armada/sopir sudah punya jadwal rute lain di jam yang sama | Ditolak/dilewati. Saat ini hanya duplikat rute+armada yang sama yang dicek (BUG-13) | |
| ADM-SC-07 | Ganti armada/sopir jadwal kosong | | Tersimpan, kapasitas dihitung ulang | |
| ADM-SC-08 ⚠️ | Ganti armada jadwal yang sudah ada penumpang / armada sudah dipakai | | Ditolak atau kursi dipetakan ulang (BUG-13) | |
| ADM-SC-09 | Ubah status jadwal | Scheduled/Active/Completed | Tersimpan | |
| ADM-SC-10 ⚠️ | Batalkan jadwal yang sudah ada penumpang | Status Cancelled | Booking dibatalkan/refund & penumpang diberi tahu (BUG-14) | |
| ADM-SC-11 | Hapus jadwal yang ada booking | | Ditolak | |
| ADM-SC-12 | Hapus jadwal kosong | | Terhapus | |

### 7.9 Refund

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-RF-01 | Buat refund (persentase) | Booking lunas, fee 10% | Nominal bersih = 90% | |
| ADM-RF-02 | Buat refund (nominal) | Fee Rp 200.000 > harga tiket | Fee dibatasi ke harga tiket, bersih 0 | |
| ADM-RF-03 ⚠️ | Refund ganda | Penumpang sudah ajukan refund (`Diajukan`), admin buat refund lagi | Ditolak (BUG-09) | |
| ADM-RF-04 | Approve refund | Dengan bukti transfer gambar ≤ 4MB | Refund `Disetujui`, booking & tiket batal, pembayaran `Refunded`, kursi lepas | |
| ADM-RF-05 | Approve ulang | Approve refund yang sudah disetujui | Ditolak dengan pesan | |
| ADM-RF-06 | Reject refund tanpa alasan / < 5 karakter | | Error validasi | |
| ADM-RF-07 ⚠️ | Reject refund dari penumpang | Tolak refund `Diajukan` | Booking & tiket kembali aktif sesuai pesan sistem (BUG-09) | |
| ADM-RF-08 | Counter refund pending | Penumpang ajukan refund, buka `/refund` | Counter bertambah (lihat BUG-09) | |

### 7.10 Laporan

| ID | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-LP-01 | Laporan per tab | `/laporan`: rekonsiliasi, keterisian kursi, manifes, refund | Semua tab tampil, angka cocok dengan data | |
| ADM-LP-02 | Filter tanggal | Ubah rentang tanggal | Angka berubah sesuai filter | |
| ADM-LP-03 | Ekspor Excel | Klik ekspor Excel | File terunduh, isi sesuai filter | |
| ADM-LP-04 | Ekspor PDF | Klik ekspor PDF | File terunduh, isi sesuai filter | |
| ADM-LP-05 | Tanpa data | Rentang tanggal tanpa transaksi | Tidak error, tampil kosong | |

### 7.11 Pengaturan

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| ADM-SET-01 | Halaman pengaturan | `/pengaturan` | 5 bagian tampil: profil, kebijakan, pembayaran, titik operasional, keamanan | |
| ADM-SET-02 | Profil perusahaan | Nama wajib, logo ≤ 2MB | Tersimpan, logo tampil | |
| ADM-SET-03 | Kebijakan tiket | Timeout 1–1440 menit, cutoff reschedule 0–168 jam | Tersimpan; nilai di luar batas ditolak | |
| ADM-SET-04 | Integrasi pembayaran | provider midtrans/xendit/none, server/client key | Tersimpan | |
| ADM-SET-05 | Titik operasional | Tambah, edit, hapus; `maps_url` harus URL | CRUD jalan, URL tidak valid ditolak | |
| ADM-SET-06 | Ganti password admin | Password lama benar + baru 8+ + konfirmasi | Berhasil | |
| ADM-SET-07 | Password lama salah | | "Kata sandi saat ini tidak cocok." | |

---

## 8. Staff Loket (role `staff`)

Login: staff@travel.test / password / STF-2026-TRAVEL.

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| STF-01 | Dashboard staff | `/admin/dashboard` | Statistik tampil | |
| STF-02 | Manajemen booking | `/admin/manajemen-booking`, filter & cari | Data booking tampil | |
| STF-03 | Informasi perjalanan | `/admin/informasi-perjalanan` | Jadwal & armada tampil | |
| STF-04 | Halaman validasi tiket | `/admin/validasi-tiket` | Tampil dengan pilihan jadwal | |
| STF-05 ⚠️ | Hak akses staf | Hapus hak `checkin_ticket` dari staf, lalu validasi tiket | Ditolak (BUG-17) | |
| STF-VT-01 | Tiket valid | Scan kode QR / nomor tiket / kode booking yang lunas, jadwal hari ini | Sukses, semua penumpang `boarded` | |
| STF-VT-02 | Format kode | Uji `QR-TRV-…`, `TRV-…` (tanpa prefix), huruf kecil | Semua ditemukan | |
| STF-VT-03 | Tiket dibatalkan | Booking `cancelled` | "Tiket Telah Dibatalkan" | |
| STF-VT-04 | Tiket sudah di-refund | Refund disetujui | "Tiket Telah Di-refund" | |
| STF-VT-05 | Tiket belum lunas | Booking pending | "Tiket Belum Lunas" | |
| STF-VT-06 | Salah jadwal | Pilih jadwal lain lalu scan | "Salah Jam Keberangkatan" | |
| STF-VT-07 | Jadwal batal / armada sudah berangkat / trip selesai / tanggal lewat | Sesuaikan data jadwal | Pesan penolakan yang sesuai | |
| STF-VT-08 ⚠️ | Penumpang sudah check-in mandiri | Penumpang lakukan USR-TK-03, staf scan | "Tiket Sudah Pernah Di-Scan", bukan "Belum Lunas" (BUG-17) | |
| STF-VT-09 | Scan dua kali | Scan tiket yang sama dua kali | Scan kedua: "Sudah Pernah Di-Scan" | |
| STF-VT-10 | Kode tidak ditemukan | Kode acak | 404 "Tiket Tidak Ditemukan" | |
| STF-VT-11 | Ubah status boarding penumpang | belum_hadir → boarded → no_show | Status penumpang & tiket berubah | |
| STF-VT-12 ⚠️ | ID penumpang tidak ada | `PATCH …/passenger/999999/status` | 404 / `success: false` (BUG-17) | |
| STF-VT-13 ⚠️ | Kode demo | Scan `TKV-UNPAID` / `TKV-7821-JKT` | Tidak boleh ada hasil palsu di production (BUG-17) | |

---

## 9. Driver (role `driver`)

Login: driver@travel.test / password / DRV-2026-TRAVEL. Pastikan driver punya jadwal hari ini (lihat Kelola Jadwal).

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| DRV-01 | Halaman driver | Buka dashboard, jadwal, kendaraan, daftar penumpang, seat map, rute, status, ubah status, info penumpang, lapor masalah, emergency, GPS, performa, reward, riwayat | Semua 200 tanpa error | |
| DRV-02 | Driver tanpa jadwal | Login driver tanpa jadwal | Halaman tampil kosong dengan rapi, tanpa error | |
| DRV-03 | Mulai penjemputan | Status `pickup` | Jadwal `Persiapan`, driver `on_duty`, penumpang dapat notifikasi | |
| DRV-04 | Berangkat | Status `departed` | Jadwal `Dalam Perjalanan`, kendaraan `beroperasi`, notifikasi terkirim | |
| DRV-05 | Tiba | Status `arrived` | Jadwal `Tiba di Tujuan` | |
| DRV-06 | Selesai | Status `completed` | Jadwal `Completed`, driver `standby`, kendaraan `siap_jalan`, booking lunas jadi `completed` | |
| DRV-07 | Status tidak valid | `status=terbang` | Error validasi | |
| DRV-08 ⚠️ | Lompat status | Langsung `completed` dari jadwal yang belum dijemput | Ditolak (BUG-18) | |
| DRV-09 ⚠️ | Selesai dengan booking belum bayar | Jadwal punya booking pending | Booking pending tidak jadi `completed` (BUG-18) | |
| DRV-10 | Update GPS | Kirim lat/lng | Koordinat & `last_gps_at` tersimpan | |
| DRV-11 | Check-in penumpang | boarded / belum_hadir | Status penumpang berubah | |
| DRV-12 ⚠️ | Aksi di jadwal driver lain | Login driver.bambang, kirim status/GPS/insiden/SOS ke jadwal driver@ | 403 (BUG-06) | |
| DRV-13 ⚠️ | Tandai lunas tunai | `mark_paid=1` untuk penumpang booking online pending di jadwal lain | Ditolak (BUG-06) | |
| DRV-14 | Lapor masalah | Kategori `ban_bocor`, severity `sedang`, deskripsi | Insiden tercatat | |
| DRV-15 | Lapor macet parah | Kategori `macet_parah` | Penumpang dapat notifikasi keterlambatan | |
| DRV-16 | Validasi laporan | Kategori/severity tidak dikenal, deskripsi kosong / > 1000 | Error validasi | |
| DRV-17 | SOS | Tekan tombol darurat | Insiden `sos_emergency` tercatat, kontak darurat tampil, log emergency tertulis | |

---

## 10. Customer Service (role `cs`)

Login: cs@travel.test / password / CS-2026-TRAVEL.

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| CS-01 | Dashboard | `/customer-service/dashboard` | Keberangkatan hari ini, pembayaran pending, refund pending tampil | |
| CS-02 | Cari booking | Cari dengan kode booking, nomor tiket, nama/email/HP pemesan atau penumpang | Hasil ditemukan untuk tiap jenis kata kunci | |
| CS-03 ⚠️ | Counter refund pending | Penumpang ajukan refund, admin buat refund `Menunggu` | Counter menghitung keduanya. Saat ini selalu 0 (BUG-09) | |
| CS-04 | Halaman CS lain | Live chat, konteks booking, riwayat chat, kirim file, eskalasi | Semua 200 | |
| CS-05 | Fungsi live chat/kirim file/eskalasi | Coba kirim pesan / file / eskalasi | Catatan: halaman ini masih statis (tidak ada endpoint simpan). Tentukan apakah memang mockup | |

---

## 11. Payment gateway & API

| ID | Skenario | Langkah / Data | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| PAY-01 | Daftar kanal | `GET /api/payment/channels` | JSON daftar kanal | |
| PAY-02 | Charge dari sesi pemilik | `POST /api/payment/charge {booking_id, payment_channel}` | Data VA/QR dikembalikan | |
| PAY-03 | Charge booking orang lain | Sesi lain | 403 `Forbidden` | |
| PAY-04 | Webhook settlement bertanda tangan valid | Signature SHA512 benar | Booking `confirmed`, tiket & email terbit 1x | |
| PAY-05 ⚠️ | Webhook tanpa signature | `POST /api/payment/webhook {"order_id":"<order pending>"}` | Ditolak 403. Saat ini booking menjadi lunas (BUG-01) | |
| PAY-06 ⚠️ | Webhook signature salah | `signature_key` acak | 403 `Invalid signature` | |
| PAY-07 | Webhook expire | `transaction_status=expire` | Booking batal, kursi lepas, notifikasi terkirim | |
| PAY-08 | Webhook order tidak dikenal / tanpa order_id | | 404 / 400 | |
| PAY-09 | Webhook dikirim dua kali | Kirim settlement 2x | Tiket & email hanya terbit sekali | |

---

## 12. Skenario lintas role (end-to-end)

| ID | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| X-01 | Alur normal | Admin buat jadwal → penumpang pesan & bayar → staf scan tiket → driver pickup → departed → arrived → completed → penumpang beri ulasan | Semua status konsisten, ulasan tampil di landing page | |
| X-02 | Alur refund | Penumpang bayar → ajukan refund → admin approve → staf scan tiket | Staf melihat "Tiket Telah Dibatalkan/Di-refund", kursi bisa dipesan lagi | |
| X-03 ⚠️ | Double booking setelah check-in | Penumpang A bayar & check-in mandiri → penumpang B memilih kursi A | Kursi A tidak bisa dipilih (BUG-05) | |
| X-04 | Booking loket vs online | Staf/admin buat booking manual pending kursi 2A → penumpang online buka seat map | Kursi 2A terisi/terkunci (cek BUG-05 untuk status kapital) | |
| X-05 | Reschedule lalu scan | Admin reschedule booking → staf scan tiket di jadwal lama dan jadwal baru | Jadwal lama: salah jadwal; jadwal baru: valid | |
| X-06 | Blokir akun aktif | User sedang login, admin memblokir | Sesi berikutnya ditolak (BUG-02) | |
| X-07 | Batal jadwal oleh admin | Jadwal punya penumpang lunas → admin set Cancelled → penumpang & staf cek tiket | Penumpang diberi tahu, staf melihat "Jadwal Trip Dibatalkan" (BUG-14) | |
