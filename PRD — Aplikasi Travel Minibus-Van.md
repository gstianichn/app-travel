# PRD — Aplikasi Travel Minibus/Van

**Versi:** 1.0  
**Status:** Draft Final  
**Platform:** Website  
**Framework:** Laravel 13  
**Frontend:** Blade + Tailwind CSS + Vanilla JavaScript  
**Database:** SQLite  
**Bahasa:** Indonesia & English  
**Timezone:** WIB (UTC+7)  
**Mata Uang:** IDR / USD berdasarkan bahasa

---

# 1. Product Overview

## 1.1 Nama Produk

**Travel Booking Website**

> Nama/branding final belum ditentukan.

## 1.2 Deskripsi Produk

Aplikasi Travel merupakan platform berbasis website yang digunakan untuk melakukan pencarian dan pemesanan tiket perjalanan antar kota menggunakan kendaraan **Minibus/Van**.

Pengguna dapat mencari rute, melihat jadwal dan harga, memilih kendaraan dan kursi, melakukan pembayaran, mendapatkan e-ticket dengan QR Code, serta melihat riwayat perjalanan.

Sistem juga menyediakan dashboard khusus untuk:

- User
- Admin Utama
- Admin Biasa
- Driver
- Customer Service

---

# 2. Product Goals

Tujuan utama aplikasi:

1. Memudahkan User mencari perjalanan antar kota.
2. Memudahkan User melakukan booking tiket secara online.
3. Memberikan informasi jadwal dan ketersediaan kursi secara jelas.
4. Menyediakan proses pembayaran digital yang mudah.
5. Menghasilkan e-ticket secara otomatis.
6. Memudahkan Admin mengelola operasional perjalanan.
7. Memudahkan Driver melihat jadwal dan penumpang.
8. Memudahkan Admin Biasa melakukan validasi tiket.
9. Menyediakan sistem refund dan pembatalan.
10. Menyediakan rating dan review perjalanan.

---

# 3. Target User

Aplikasi ditujukan untuk **semua kalangan** yang membutuhkan transportasi perjalanan antar kota menggunakan Minibus/Van.

---

# 4. User Roles

## 4.1 User

User dapat:

- Registrasi
- Login
- Verifikasi OTP
- Lupa password
- Mencari rute
- Melihat jadwal
- Melihat harga
- Melihat kendaraan
- Memilih kursi
- Melakukan booking
- Melakukan pembayaran
- Mendapatkan e-ticket
- Melihat riwayat booking
- Membatalkan booking
- Mengajukan refund
- Melihat riwayat refund
- Memberikan rating
- Memberikan review
- Menggunakan promo
- Melihat notifikasi

---

## 4.2 Admin Utama

Admin Utama memiliki akses penuh terhadap sistem.

Fitur:

- Dashboard
- Kelola Admin Biasa
- Kelola User
- Kelola Driver
- Kelola kendaraan
- Kelola rute
- Kelola jadwal
- Kelola booking
- Kelola pembayaran
- Kelola refund
- Kelola promo
- Kelola review
- Kelola notifikasi
- Monitoring perjalanan
- Audit log
- Pengaturan sistem
- Backup database
- Export data

---

## 4.3 Admin Biasa

Admin Biasa memiliki akses operasional.

Fitur utama:

- Melihat booking
- Validasi e-ticket
- Scan QR Code
- Validasi menggunakan booking code
- Mengubah status boarding
- Melihat informasi perjalanan

Admin Biasa tidak memiliki seluruh hak akses Admin Utama.

---

## 4.4 Driver

Driver memiliki dashboard khusus untuk operasional perjalanan.

Fitur:

- Melihat jadwal perjalanan
- Melihat kendaraan yang ditugaskan
- Melihat daftar penumpang
- Melihat seat map
- Melihat rute perjalanan
- Melihat status perjalanan
- Mengubah status perjalanan
- Melihat informasi penumpang
- Melaporkan masalah perjalanan
- Emergency Center
- GPS Tracking saat perjalanan aktif
- Melihat performa
- Melihat reward
- Melihat riwayat perjalanan

---

## 4.5 Customer Service

Customer Service memiliki sistem komunikasi dengan User.

Fitur:

- Live chat
- Melihat konteks booking
- Melihat riwayat chat
- Membalas pesan
- Mengirim gambar/file
- Melakukan eskalasi ke Admin

Customer Service tidak memiliki akses terhadap:

- Pembayaran
- Refund
- Operasional kendaraan
- Pengaturan sistem

---

# 5. Authentication

## 5.1 Login

Metode:

- Email
- Password
- OTP ketika diperlukan

## 5.2 OTP

OTP digunakan untuk:

- Registrasi
- Lupa password
- Login dari perangkat baru

OTP menggunakan:

- 6 digit
- Countdown
- Resend OTP
- Change email/phone jika diperlukan

## 5.3 Failed Login

Jika terjadi percobaan login gagal berulang:

- Akun dikunci sementara
- User mendapatkan notifikasi

## 5.4 Login Information

Sistem menyimpan:

- Waktu login terakhir
- Device
- Status login

---

# 6. Homepage

Homepage memiliki struktur:

1. Hero Section
2. Search Ticket
3. Popular Routes
4. Promo
5. Travel Advantages
6. How to Book
7. Fleet
8. Rating & Review
9. FAQ
10. CTA
11. Footer

## 6.1 Hero

Headline:

> Perjalanan Nyaman, Sampai Tujuan.

Visual:

- Minibus/Van
- Jalan
- Pemandangan perjalanan

Warna utama:

- Green
- White
- Gray

---

# 7. Search Ticket

Form pencarian:

- From
- To
- Date
- Number of passengers
- Departure time
- Search

City menggunakan:

- Input kota
- Autocomplete

Jika rute tidak tersedia:

> Rute tidak tersedia

User tidak harus login untuk melakukan pencarian.

---

# 8. Route

Setiap route memiliki:

- Kota asal
- Kota tujuan
- Harga tiket
- Status
- Promo
- Jadwal

URL menggunakan format SEO-friendly:

```text
/rute/bandung-jakarta
```

Harga tiket:

> Harga tetap berdasarkan rute.

Contoh:

```text
Bandung → Jakarta
Rp150.000
```

---

# 9. Schedule

Admin membuat jadwal secara manual.

Informasi jadwal:

- Route
- Vehicle
- Driver
- Departure
- Arrival
- Status

Status perjalanan:

```text
Scheduled
↓
Boarding
↓
Departed
↓
In Progress
↓
Completed
```

Status alternatif:

```text
Cancelled
```

---

# 10. Vehicle Management

## 10.1 Data Kendaraan

Data minimal:

- Nomor plat
- Tipe kendaraan
- Foto
- Kapasitas penumpang
- Fasilitas
- Status

## 10.2 Fasilitas Kendaraan

Fasilitas:

- AC
- USB Charger
- Bagasi

Tidak menggunakan Wi-Fi.

## 10.3 Vehicle Status

```text
Available
Assigned
On Trip
Maintenance
Inactive
```

## 10.4 Maintenance

Data maintenance:

- Tanggal
- Jenis maintenance

---

# 11. Seat Management

Admin menentukan:

- Jumlah baris
- Jumlah kursi setiap baris

Contoh:

```text
Driver

A1  A2  A3

B1  B2  B3

C1  C2  C3
```

Kursi Driver:

> Selalu berada di bagian paling depan dan otomatis.

Nomor kursi menggunakan format:

```text
A1
A2
B1
B2
C1
C2
```

## 11.1 Seat Status

Status:

- Available
- Selected
- Occupied

## 11.2 Seat Lock

Kursi yang dipilih akan dikunci selama:

**5 menit**

Jika pembayaran tidak selesai:

- Booking dibatalkan
- Kursi dilepaskan

---

# 12. Booking Flow

Flow utama:

```text
Landing Page
↓
Search Route
↓
View Schedule
↓
Choose Trip
↓
Vehicle Detail
↓
Choose Seat
↓
Login / Register
↓
Passenger Data
↓
Confirmation
↓
Payment
↓
Payment Success
↓
E-Ticket
```

---

# 13. Passenger Data

Data pemesan:

- Nama
- Nomor telepon
- Email

Data penumpang:

- Nama
- Nomor telepon jika diperlukan
- Nomor kursi

Optional:

- Special request

---

# 14. Checkout

Checkout terdiri dari:

### Step 1 — Passenger Data

Data penumpang.

### Step 2 — Confirmation

Konfirmasi:

- Route
- Date
- Time
- Vehicle
- Driver
- Seat
- Passenger
- Price
- Promo
- Total

### Step 3 — Payment

User memilih metode pembayaran.

### Step 4 — Digital Ticket

E-ticket dibuat setelah pembayaran berhasil.

Checkout memiliki batas waktu:

**10 menit**

---

# 15. Pricing

Harga tiket:

> Tetap berdasarkan rute.

Semua kategori penumpang memiliki harga yang sama.

Tidak ada biaya tambahan.

Rumus:

```text
Total = Harga Tiket × Jumlah Penumpang
```

Jika promo tersedia:

```text
Total = Harga Tiket × Jumlah Penumpang - Diskon
```

---

# 16. Promo

Promo diterapkan otomatis.

User tidak perlu memasukkan kode promo.

## Promo Rules

Admin dapat menentukan:

- Rute
- Tanggal mulai
- Tanggal berakhir
- Batas penggunaan per User

Promo otomatis diterapkan ketika User melakukan checkout dan memenuhi syarat.

Jenis diskon:

> Diskon langsung pada harga tiket.

---

# 17. Payment

Metode pembayaran:

- Transfer Bank
- E-Wallet
- QRIS

Payment gateway:

**Midtrans**

Verifikasi pembayaran:

> Otomatis melalui payment gateway.

User tidak perlu upload bukti pembayaran.

## Payment Status

```text
Pending
↓
Processing
↓
Paid / Failed
↓
Expired
↓
Refunded
```

Jika pembayaran melewati batas waktu:

- Booking dibatalkan
- Kursi dilepaskan

---

# 18. E-Ticket

E-ticket dibuat:

> Langsung setelah pembayaran berhasil.

E-ticket tersedia di aplikasi dan dapat:

> Diunduh sebagai gambar.

Informasi e-ticket:

- Booking code
- QR Code
- Nama penumpang
- Route
- Date
- Departure
- Arrival
- Vehicle
- Driver
- Seat
- Total price

---

# 19. QR Ticket Validation

QR Code digunakan untuk validasi tiket.

Validator:

> Admin Biasa.

Flow:

```text
Scan QR
↓
Validasi Tiket
↓
Status = Boarded
```

Jika QR Code sudah pernah digunakan:

```text
Tiket sudah digunakan
```

Jika QR Code tidak terbaca:

```text
Booking Code
↓
Cari Tiket
↓
Validasi Tiket
↓
Boarded
```

Tidak memerlukan persetujuan Admin Utama.

---

# 20. Ticket Validation Rules

Sistem menolak tiket jika:

- QR Code sudah digunakan
- Tiket tidak sesuai jadwal
- Tiket sudah melewati jadwal keberangkatan

Status tiket setelah validasi:

```text
Boarded
```

---

# 21. Booking Status

```text
Pending
↓
Waiting Payment
↓
Paid
↓
Confirmed
↓
Completed
```

Alternatif:

```text
Cancelled
```

---

# 22. Cancellation

User dapat membatalkan tiket:

> Maksimal H-1 sebelum keberangkatan.

Admin menentukan persentase refund.

Flow:

```text
User Cancel Booking
↓
Refund Request
↓
Admin Review
↓
Approve / Reject
```

---

# 23. Refund

Refund diproses oleh Admin.

Admin dapat:

- Approve
- Reject

Dana refund dikembalikan melalui:

> Metode pembayaran yang digunakan saat booking.

## Refund Status

```text
Menunggu
↓
Disetujui
↓
Berhasil
```

User menerima informasi refund melalui:

> Notifikasi dalam aplikasi.

User memiliki halaman:

> Riwayat Refund

---

# 24. Driver Management

Data Driver:

- Nama
- Nomor telepon
- Foto profil
- Nomor SIM
- Masa berlaku SIM
- Status Driver

Status Driver:

```text
Available
On Trip
Maintenance
Inactive
```

---

# 25. Driver Assignment

Sistem dapat melakukan assignment Driver secara otomatis.

Jika Driver perlu diganti:

1. Sistem mencari Driver yang tersedia.
2. Driver dipilih berdasarkan jarak terdekat dari titik keberangkatan.
3. Jika tidak tersedia:
   - Trip menjadi `Menunggu Assignment`
   - Admin mendapat notifikasi.

---

# 26. Vehicle Assignment

Sistem melakukan validasi konflik jadwal kendaraan.

Jika kendaraan tidak tersedia:

- Sistem mencari kendaraan dengan jadwal terdekat.

Jika tidak ada kendaraan yang dapat digunakan tanpa konflik:

- Trip otomatis dibatalkan
- User mendapat notifikasi
- Driver mendapat notifikasi
- Refund request dibuat
- Alasan dicatat
- Audit log dibuat

---

# 27. Driver Trip Status

Driver dapat mengubah status:

```text
Belum Berangkat
↓
Berangkat
↓
Sedang Berlangsung
↓
Selesai
```

Setiap perubahan status menyimpan:

- Status
- Waktu
- Driver
- Trip

---

# 28. GPS Tracking

GPS digunakan ketika Driver sedang melakukan perjalanan.

Fitur:

- Browser GPS
- Real-time location
- Update berkala
- Status tracking aktif/nonaktif
- Permission control

GPS hanya aktif selama:

> Active Trip

GPS otomatis berhenti setelah perjalanan selesai.

---

# 29. Emergency Center

Driver memiliki Emergency Center.

Jenis incident:

- Accident
- Vehicle Problem
- Trip Disruption
- Passenger Issue
- Medical
- Other

Emergency Center menyediakan:

- Emergency button
- Lokasi real-time
- Kontak Admin/CS
- Status penanganan
- Riwayat incident

Emergency memiliki prioritas tinggi.

---

# 30. Driver & Admin Communication

Sistem menyediakan:

- Real-time chat
- Voice call
- Quick message

Komunikasi memiliki konteks:

- Trip
- Driver
- Vehicle
- Passenger

Emergency message memiliki prioritas tinggi.

---

# 31. Driver & Passenger Communication

Passenger Contact Center menyediakan:

- Chat
- Phone

Nomor telepon dapat menggunakan nomor yang dilindungi.

Komunikasi hanya tersedia selama:

> Active Trip

Aktivitas komunikasi dicatat oleh sistem.

---

# 32. Boarding

Metode boarding:

> Manual menggunakan QR Code oleh Admin Biasa.

Jika QR tidak terbaca:

> Admin mencari menggunakan booking code.

Setelah validasi:

```text
Boarded
```

---

# 33. No Show

Jika penumpang tidak hadir:

```text
No Show
```

Konsekuensi:

> Tiket hangus dan tidak mendapatkan refund.

---

# 34. Trip Completion

Setelah perjalanan selesai, sistem menyimpan Trip Completion Report.

Informasi:

- Status perjalanan
- Daftar penumpang
- Boarding status
- Waktu keberangkatan
- Waktu selesai
- Vehicle
- Driver
- Issues
- Incident
- Driver notes

---

# 35. Rating & Review

Setelah perjalanan selesai, User dapat memberikan rating.

Rating:

**1–5 bintang**

Kategori:

- Driver
- Vehicle
- Trip
- Punctuality
- Comfort
- Cleanliness
- Service
- Safety

User dapat menambahkan komentar.

---

# 36. Review Moderation

Review tidak langsung tampil.

Admin dapat:

- Approve
- Reject
- Hide

---

# 37. Driver Performance

Performance Score berdasarkan:

- Rating
- Punctuality
- Completion rate
- Trip count
- Boarding success
- Complaints
- Incident
- Admin evaluation

Dashboard menampilkan:

- Overall score
- Breakdown indikator
- Trend
- Perbandingan periode
- Insight

---

# 38. Driver Reward

Reward Driver terdiri dari:

- Badge
- Ranking
- Bonus performance

Ranking dapat berdasarkan:

- Periode
- Kategori
- Route

Admin membuat aturan bonus.

Sistem menghitung bonus.

Admin melakukan approval.

Bonus dibayarkan melalui:

> Transfer ke rekening bank Driver.

Driver mengisi:

- Nomor rekening
- Nama pemilik rekening

Admin melakukan verifikasi manual.

Driver dapat melihat:

> Riwayat bonus berdasarkan periode.

---

# 39. Notification

Notifikasi digunakan untuk:

- Booking
- Payment
- Refund
- Trip
- Schedule
- Driver
- Emergency
- Maintenance
- Important activity

Semua role memiliki:

> Notification History

Setiap notifikasi memiliki:

- Category
- Status
- Timestamp

Notifikasi bersifat otomatis.

---

# 40. Customer Service

Customer Service menggunakan sistem:

> Live Chat

Fitur:

- Chat history
- Booking context
- Admin/CS reply
- Priority
- Escalation

File upload:

- JPG
- JPEG
- PNG
- WebP

Maksimal:

**2 MB**

---

# 41. User Dashboard

Dashboard User menampilkan:

- Active Booking
- Booking History
- Digital Ticket
- Promo
- Recommended Routes
- Notifications
- Loyalty points
- Membership tier

---

# 42. Loyalty Program

User memiliki sistem:

> Points + Member Tier

Tier:

```text
Silver
Gold
Platinum
```

Benefit:

- Badge
- Bonus points
- Priority promo

Points dapat ditukar dengan:

> Promo Voucher

Masa berlaku:

- Points: 6 bulan
- Voucher: 7 hari

Tidak terdapat referral program.

---

# 43. Admin Dashboard

Dashboard Admin menggunakan desain premium.

Menampilkan:

- Total booking
- Revenue
- Payment
- Refund
- Active trips
- Vehicle status
- Driver status
- User
- Charts
- Monitoring
- Activity log
- Alert center

---

# 44. Operations Center

Admin dapat melihat:

- Live map
- Vehicle position
- Trip status
- Driver
- Passenger
- Emergency alert

Admin dapat melakukan quick action.

---

# 45. Alert Center

Admin mendapatkan alert:

- Booking
- Payment
- Refund
- Emergency
- Trip changes
- Maintenance
- Important activity

Priority:

```text
High
Normal
Low
```

Emergency dan trip changes memiliki prioritas tinggi.

---

# 46. Audit Log

Audit log hanya mencatat aktivitas penting.

Contoh:

- Refund
- Delete data
- Perubahan trip penting
- User/Admin changes
- Important settings

Data audit:

- User/Admin
- Activity
- Timestamp
- Target
- Description

---

# 47. Backup

Backup database dilakukan secara manual oleh Admin.

---

# 48. Export Data

Admin dapat melakukan export:

- Booking
- Payment
- User
- Trip

Format:

- Excel
- CSV

Filter:

- Date
- Status
- Route

---

# 49. Multilingual

Bahasa:

```text
Indonesia
English
```

Currency:

```text
Indonesia → IDR
English → USD
```

Timezone:

```text
WIB / UTC+7
```

---

# 50. Date & Time Format

Tanggal:

```text
15 Sep 2026
```

Format:

```text
DD MMM YYYY
```

Waktu:

```text
08:30 AM
```

Format:

> 12-hour.

---

# 51. UI/UX Design

## Visual Style

Konsep:

> Fresh & Friendly

Karakter:

- Modern
- Clean
- Friendly
- Premium

## Color

Primary:

> Green

Supporting:

- White
- Gray

## Component

Border radius:

> 12–16px

Style:

- Rounded
- Soft shadow
- Clean input
- Subtle hover

---

# 52. Dark Mode

Dark mode tersedia.

Default:

> Light Mode

Brand green tetap digunakan sebagai warna utama.

---

# 53. Animation

Menggunakan:

> Subtle Premium Animation

Tidak menggunakan animasi berlebihan.

---

# 54. Responsive Design

Website harus responsive untuk:

- Desktop
- Tablet
- Mobile

Desktop dan mobile sama-sama menjadi prioritas.

---

# 55. Mobile Navigation

Bottom Navigation:

```text
Home
Search
Orders
Profile
```

Hamburger Menu:

```text
Promo
Help
About
Terms
Privacy
Logout
```

---

# 56. Technology Stack

## Backend

```text
Laravel 13
PHP
```

## Frontend

```text
Blade
Tailwind CSS
Vanilla JavaScript
```

## Database

```text
SQLite
```

## Payment

```text
Midtrans
```

## Maps

```text
Google Maps
```

## Email

```text
Gmail SMTP
```

## Storage

```text
Laravel Public Storage
```

---

# 57. Architecture

Aplikasi menggunakan:

> Full Laravel Blade

Tidak menggunakan frontend terpisah.

Tidak menggunakan:

- React
- Vue
- Separate API frontend

Struktur utama:

```text
Laravel
├── Models
├── Controllers
├── Middleware
├── Services
├── Notifications
├── Requests
├── Policies
├── Views
└── Routes
```

---

# 58. Google Maps

Google Maps digunakan untuk:

- Menampilkan lokasi
- Marker
- Route
- Estimasi jarak
- Estimasi waktu

Tidak menggunakan fitur advanced live tracking dari Google Maps.

GPS Driver tetap menggunakan browser GPS.

---

# 59. SEO

SEO menggunakan basic SEO:

- Title
- Description
- Keyword

URL menggunakan SEO-friendly route.

Contoh:

```text
/rute/bandung-jakarta
```

---

# 60. Performance

Optimasi dasar:

- Compress image
- Optimize assets
- Lazy loading jika diperlukan
- Mengurangi asset yang tidak digunakan

---

# 61. Accessibility

Implementasi accessibility dasar:

- Color contrast
- Form labels
- Alt text
- Keyboard navigation

---

# 62. Security

Sistem harus menerapkan:

- Authentication
- Authorization
- Middleware
- Role permission
- OTP
- Temporary account lock
- CSRF protection
- Validation
- File validation
- Secure payment callback
- Audit log

---

# 63. Core User Journey

```text
User
 │
 ▼
Landing Page
 │
 ▼
Search Route
 │
 ▼
Schedule
 │
 ▼
Trip Detail
 │
 ▼
Seat Selection
 │
 ▼
Login / Register
 │
 ▼
Passenger Data
 │
 ▼
Confirmation
 │
 ▼
Payment
 │
 ├── Failed → Retry
 │
 └── Success
        │
        ▼
     E-Ticket
        │
        ▼
   QR Validation
        │
        ▼
      Boarded
        │
        ▼
     Trip Started
        │
        ▼
      Completed
        │
        ▼
   Rating & Review
```

---

# 64. Admin Operational Flow

```text
Admin
 │
 ├── Manage Route
 │
 ├── Manage Schedule
 │
 ├── Manage Vehicle
 │
 ├── Manage Driver
 │
 ├── Monitor Booking
 │
 ├── Monitor Payment
 │
 ├── Manage Refund
 │
 ├── Manage Promo
 │
 ├── Moderate Review
 │
 └── Monitor Trip
```

---

# 65. Driver Operational Flow

```text
Login
 ↓
Dashboard
 ↓
View Assigned Trip
 ↓
View Vehicle
 ↓
View Passenger
 ↓
Boarding
 ↓
Depart
 ↓
GPS Active
 ↓
Trip In Progress
 ↓
Complete Trip
 ↓
Trip Report
```

---

# 66. MVP Scope

Prioritas MVP:

## Authentication

- Register
- Login
- OTP
- Forgot Password
- Role

## User

- Search
- Schedule
- Trip detail
- Seat selection
- Booking
- Payment
- E-ticket
- Booking history

## Admin

- Route
- Schedule
- Vehicle
- Driver
- Booking
- Payment
- Refund

## Driver

- Dashboard
- Schedule
- Passenger
- Trip status

## Ticket

- QR Code
- QR validation
- Booking code fallback

---

# 67. Phase 2

Fitur setelah MVP:

- GPS tracking
- Emergency Center
- Live chat
- Loyalty
- Reward Driver
- Advanced monitoring
- Performance dashboard
- Advanced analytics

---

# 68. Success Metrics

Keberhasilan aplikasi dapat diukur melalui:

- Jumlah booking berhasil
- Conversion rate
- Payment success rate
- Booking cancellation rate
- Refund rate
- Jumlah perjalanan selesai
- Jumlah user aktif
- Rating rata-rata
- Tingkat penggunaan promo
- Driver completion rate

---

# 69. Acceptance Criteria Utama

## Booking

- User dapat mencari rute.
- User dapat memilih jadwal.
- User dapat memilih kursi.
- Kursi tidak dapat dipilih oleh User lain ketika sedang terkunci.
- Booking berhasil setelah pembayaran berhasil.

## Payment

- Payment gateway dapat menerima pembayaran.
- Status pembayaran diperbarui otomatis.
- Booking expired jika pembayaran melewati batas waktu.

## E-ticket

- E-ticket otomatis dibuat setelah pembayaran berhasil.
- QR Code tersedia.
- Tiket dapat diunduh sebagai gambar.

## QR Validation

- Admin Biasa dapat melakukan scan.
- QR valid mengubah status menjadi Boarded.
- QR yang sudah digunakan ditolak.
- Tiket tidak sesuai jadwal ditolak.
- Tiket kadaluarsa ditolak.
- Booking code dapat digunakan sebagai fallback.

## Refund

- User dapat mengajukan pembatalan maksimal H-1.
- Admin dapat approve/reject.
- Refund kembali ke metode pembayaran awal.
- User dapat melihat riwayat refund.

## Review

- User dapat memberikan rating setelah perjalanan selesai.
- Admin dapat approve/reject/hide review.

---

# 70. Future Development

Pengembangan selanjutnya dapat mencakup:

- Native mobile application
- Push notification
- Advanced GPS tracking
- Dynamic pricing
- AI recommendation
- Advanced analytics
- Automated customer service
- Driver optimization
- Route optimization

---

# 71. Final Product Flow

```text
                    ┌──────────────┐
                    │    USER      │
                    └──────┬───────┘
                           │
                           ▼
                    Search Travel
                           │
                           ▼
                    Choose Schedule
                           │
                           ▼
                    Choose Seat
                           │
                           ▼
                    Passenger Data
                           │
                           ▼
                       Checkout
                           │
                           ▼
                       Payment
                           │
                     ┌─────┴─────┐
                     │           │
                   Failed      Success
                     │           │
                     ▼           ▼
                   Retry      E-Ticket
                                 │
                                 ▼
                           QR Validation
                                 │
                                 ▼
                              Boarded
                                 │
                                 ▼
                              Travel
                                 │
                                 ▼
                             Completed
                                 │
                                 ▼
                         Rating & Review


ADMIN
 │
 ├── Route
 ├── Schedule
 ├── Vehicle
 ├── Driver
 ├── Booking
 ├── Payment
 ├── Refund
 ├── Promo
 ├── Review
 └── Monitoring


DRIVER
 │
 ├── Schedule
 ├── Vehicle
 ├── Passenger
 ├── Trip Status
 ├── GPS
 ├── Emergency
 └── Trip Report


CUSTOMER SERVICE
 │
 ├── Live Chat
 ├── Booking Context
 ├── Chat History
 └── Escalation
```

---

# 72. Conclusion

Aplikasi Travel ini dirancang sebagai platform booking transportasi **Minibus/Van antar kota** yang mengutamakan kemudahan booking, pengelolaan operasional, pembayaran digital, e-ticket, validasi QR, serta monitoring perjalanan.

Sistem menggunakan Laravel 13 dengan Blade, Tailwind CSS, Vanilla JavaScript, dan SQLite sehingga cocok untuk dikembangkan secara bertahap oleh developer pemula.

Prioritas pengembangan adalah memastikan **alur booking sampai e-ticket berjalan stabil terlebih dahulu**, kemudian mengembangkan fitur operasional seperti Driver, GPS, Emergency Center, Customer Service, Loyalty, dan Performance Dashboard.