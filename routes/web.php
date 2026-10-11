<?php

use App\Http\Controllers\AdminBiasaDashboardController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CustomerServiceDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Pemesanan Tiket (tanpa wajib login)
|--------------------------------------------------------------------------
| Pick a seat, fill in contact details and pay. Guests can do all of it;
| BookingAccess keeps each guest booking tied to the session that made it.
*/

Route::get('/user/booking/pilih-kursi/{schedule}', [
    UserDashboardController::class,
    'selectSeat',
])->name('dashboard.user.booking.pilih-kursi');

Route::post('/user/booking/lock-seats', [
    UserDashboardController::class,
    'lockSeats',
])->name('dashboard.user.booking.lock-seats');

Route::get('/user/booking/data-penumpang/{booking}', [
    UserDashboardController::class,
    'passengerData',
])->name('dashboard.user.booking.data-penumpang');

Route::post('/user/booking/validate-promo', [
    UserDashboardController::class,
    'validatePromo',
])->name('dashboard.user.booking.validate-promo');

Route::post('/user/booking/save-passenger/{booking}', [
    UserDashboardController::class,
    'savePassengerData',
])->name('dashboard.user.booking.save-passenger');

Route::get('/user/booking/pembayaran/{booking}', [
    UserDashboardController::class,
    'payment',
])->name('dashboard.user.booking.pembayaran');

Route::post('/user/booking/pembayaran/{booking}/switch', [
    UserDashboardController::class,
    'switchPaymentMethod',
])->name('dashboard.user.booking.switch-payment');

Route::get('/user/booking/pembayaran/{booking}/status', [
    UserDashboardController::class,
    'checkPaymentStatus',
])->name('dashboard.user.booking.payment-status');

Route::post('/user/booking/simulate-payment/{booking}', [
    UserDashboardController::class,
    'simulatePayment',
])->name('dashboard.user.booking.simulate-payment');

Route::get('/user/booking/sukses/{booking}', [
    UserDashboardController::class,
    'paymentSuccess',
])->name('dashboard.user.booking.sukses');

// E-ticket link from the confirmation email (works without logging in).
Route::get('/e-tiket/{booking}', [
    UserDashboardController::class,
    'eTicket',
])->middleware('signed')->name('booking.e-ticket');

/*
|--------------------------------------------------------------------------
| Admin Utama
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin_utama,admin'])
    ->group(function () {

        Route::get('/dashboard/admin', [
            AdminDashboardController::class,
            'index',
        ])->name('dashboard.admin.index');

        Route::get('/kelola-user', [
            AdminDashboardController::class,
            'users',
        ])->name('dashboard.admin.kelola-user.index');

        Route::get('/kelola-user/create', [
            AdminDashboardController::class,
            'createUser',
        ])->name('dashboard.admin.kelola-user.create');

        Route::post('/kelola-user', [
            AdminDashboardController::class,
            'storeUser',
        ])->name('dashboard.admin.kelola-user.store');

        Route::get('/kelola-user/{user}', [
            AdminDashboardController::class,
            'showUser',
        ])->name('dashboard.admin.kelola-user.show');

        Route::get('/kelola-user/{user}/edit', [
            AdminDashboardController::class,
            'editUser',
        ])->name('dashboard.admin.kelola-user.edit');

        Route::put('/kelola-user/{user}', [
            AdminDashboardController::class,
            'updateUser',
        ])->name('dashboard.admin.kelola-user.update');

        Route::patch('/kelola-user/{user}/toggle-status', [
            AdminDashboardController::class,
            'toggleUserStatus',
        ])->name('dashboard.admin.kelola-user.toggle-status');

        Route::patch('/kelola-user/{user}/reset-password', [
            AdminDashboardController::class,
            'resetPassword',
        ])->name('dashboard.admin.kelola-user.reset-password');

        Route::delete('/kelola-user/{user}', [
            AdminDashboardController::class,
            'destroyUser',
        ])->name('dashboard.admin.kelola-user.destroy');

        /*
        |--------------------------------------------------------------------------
        | Kelola Staff (Profil Cabang/Loket, Hak Akses, Status Akun)
        |--------------------------------------------------------------------------
        */
        Route::get('/kelola-staff', [
            AdminDashboardController::class,
            'staffIndex',
        ])->name('dashboard.admin.kelola-staff.index');

        Route::get('/kelola-staff/create', [
            AdminDashboardController::class,
            'createStaff',
        ])->name('dashboard.admin.kelola-staff.create');

        Route::post('/kelola-staff', [
            AdminDashboardController::class,
            'storeStaff',
        ])->name('dashboard.admin.kelola-staff.store');

        Route::get('/kelola-staff/{staff}', [
            AdminDashboardController::class,
            'showStaff',
        ])->name('dashboard.admin.kelola-staff.show');

        Route::get('/kelola-staff/{staff}/edit', [
            AdminDashboardController::class,
            'editStaff',
        ])->name('dashboard.admin.kelola-staff.edit');

        Route::put('/kelola-staff/{staff}', [
            AdminDashboardController::class,
            'updateStaff',
        ])->name('dashboard.admin.kelola-staff.update');

        Route::patch('/kelola-staff/{staff}/toggle-status', [
            AdminDashboardController::class,
            'toggleStaffStatus',
        ])->name('dashboard.admin.kelola-staff.toggle-status');

        Route::patch('/kelola-staff/{staff}/reset-password', [
            AdminDashboardController::class,
            'resetStaffPassword',
        ])->name('dashboard.admin.kelola-staff.reset-password');

        Route::delete('/kelola-staff/{staff}', [
            AdminDashboardController::class,
            'destroyStaff',
        ])->name('dashboard.admin.kelola-staff.destroy');

        /*
        |--------------------------------------------------------------------------
        | Kelola Driver
        |--------------------------------------------------------------------------
        */
        Route::get('/kelola-driver', [
            AdminDashboardController::class,
            'driverIndex',
        ])->name('dashboard.admin.kelola-driver.index');

        Route::get('/kelola-driver/create', [
            AdminDashboardController::class,
            'createDriver',
        ])->name('dashboard.admin.kelola-driver.create');

        Route::post('/kelola-driver', [
            AdminDashboardController::class,
            'storeDriver',
        ])->name('dashboard.admin.kelola-driver.store');

        Route::get('/kelola-driver/{driver}', [
            AdminDashboardController::class,
            'showDriver',
        ])->name('dashboard.admin.kelola-driver.show');

        Route::get('/kelola-driver/{driver}/edit', [
            AdminDashboardController::class,
            'editDriver',
        ])->name('dashboard.admin.kelola-driver.edit');

        Route::put('/kelola-driver/{driver}', [
            AdminDashboardController::class,
            'updateDriver',
        ])->name('dashboard.admin.kelola-driver.update');

        Route::patch('/kelola-driver/{driver}/status', [
            AdminDashboardController::class,
            'updateDriverStatus',
        ])->name('dashboard.admin.kelola-driver.update-status');

        Route::delete('/kelola-driver/{driver}', [
            AdminDashboardController::class,
            'destroyDriver',
        ])->name('dashboard.admin.kelola-driver.destroy');

        /*
        |--------------------------------------------------------------------------
        | Memantau Kendaraan (Fleet Monitoring, Legalitas KIR & Pajak STNK)
        |--------------------------------------------------------------------------
        */
        Route::get('/memantau-kendaraan', [
            AdminDashboardController::class,
            'vehicleIndex',
        ])->name('dashboard.admin.memantau-kendaraan.index');

        Route::get('/memantau-kendaraan/create', [
            AdminDashboardController::class,
            'createVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.create');

        Route::post('/memantau-kendaraan', [
            AdminDashboardController::class,
            'storeVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.store');

        Route::get('/memantau-kendaraan/{vehicle}', [
            AdminDashboardController::class,
            'showVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.show');

        Route::get('/memantau-kendaraan/{vehicle}/edit', [
            AdminDashboardController::class,
            'editVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.edit');

        Route::put('/memantau-kendaraan/{vehicle}', [
            AdminDashboardController::class,
            'updateVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.update');

        Route::patch('/memantau-kendaraan/{vehicle}/status', [
            AdminDashboardController::class,
            'updateVehicleStatus',
        ])->name('dashboard.admin.memantau-kendaraan.update-status');

        Route::delete('/memantau-kendaraan/{vehicle}', [
            AdminDashboardController::class,
            'destroyVehicle',
        ])->name('dashboard.admin.memantau-kendaraan.destroy');

        /*
        |--------------------------------------------------------------------------
        | Pemesanan Tiket (Seat Map, Input Manual, Manifes, Reschedule)
        |--------------------------------------------------------------------------
        */
        Route::get('/pemesanan', [
            AdminDashboardController::class,
            'bookingIndex',
        ])->name('dashboard.admin.pemesanan.index');

        Route::get('/pemesanan/create-manual', [
            AdminDashboardController::class,
            'createManualBooking',
        ])->name('dashboard.admin.pemesanan.create-manual');

        Route::post('/pemesanan/manual', [
            AdminDashboardController::class,
            'storeManualBooking',
        ])->name('dashboard.admin.pemesanan.store-manual');

        Route::get('/pemesanan/jadwal/{schedule}/seat-map', [
            AdminDashboardController::class,
            'bookingSeatMap',
        ])->name('dashboard.admin.pemesanan.seat-map');

        Route::get('/pemesanan/jadwal/{schedule}/manifes', [
            AdminDashboardController::class,
            'tripManifest',
        ])->name('dashboard.admin.pemesanan.manifes');

        Route::get('/pemesanan/{booking}', [
            AdminDashboardController::class,
            'showBooking',
        ])->name('dashboard.admin.pemesanan.show');

        Route::get('/pemesanan/{booking}/reschedule', [
            AdminDashboardController::class,
            'rescheduleBookingForm',
        ])->name('dashboard.admin.pemesanan.reschedule');

        Route::put('/pemesanan/{booking}/reschedule', [
            AdminDashboardController::class,
            'updateRescheduleBooking',
        ])->name('dashboard.admin.pemesanan.reschedule.update');

        Route::patch('/pemesanan/{booking}/cancel', [
            AdminDashboardController::class,
            'cancelBooking',
        ])->name('dashboard.admin.pemesanan.cancel');

        /*
        |--------------------------------------------------------------------------
        | Pembayaran (Invoice, Verifikasi Manual Bukti Transfer, Gateway Log)
        |--------------------------------------------------------------------------
        */
        Route::get('/pembayaran', [
            AdminDashboardController::class,
            'paymentIndex',
        ])->name('dashboard.admin.pembayaran.index');

        Route::get('/pembayaran/{payment}', [
            AdminDashboardController::class,
            'showPayment',
        ])->name('dashboard.admin.pembayaran.show');

        Route::patch('/pembayaran/{payment}/verify', [
            AdminDashboardController::class,
            'verifyPayment',
        ])->name('dashboard.admin.pembayaran.verify');

        Route::post('/pembayaran/{payment}/upload-proof', [
            AdminDashboardController::class,
            'uploadPaymentProof',
        ])->name('dashboard.admin.pembayaran.upload-proof');

        Route::post('/pembayaran/{payment}/sync-gateway', [
            AdminDashboardController::class,
            'syncGatewayStatus',
        ])->name('dashboard.admin.pembayaran.sync-gateway');

        /*
        |--------------------------------------------------------------------------
        | Rute Perjalanan & Jadwal Keberangkatan (Pool, Pricing, Assignment)
        |--------------------------------------------------------------------------
        */
        Route::get('/rute', [
            AdminDashboardController::class,
            'routeIndex',
        ])->name('dashboard.admin.rute.index');

        Route::post('/rute', [
            AdminDashboardController::class,
            'storeRoute',
        ])->name('dashboard.admin.rute.store');

        Route::put('/rute/{route}', [
            AdminDashboardController::class,
            'updateRoute',
        ])->name('dashboard.admin.rute.update');

        Route::patch('/rute/{route}/toggle-status', [
            AdminDashboardController::class,
            'toggleRouteStatus',
        ])->name('dashboard.admin.rute.toggle-status');

        Route::delete('/rute/{route}', [
            AdminDashboardController::class,
            'destroyRoute',
        ])->name('dashboard.admin.rute.destroy');

        Route::post('/rute/lokasi', [
            AdminDashboardController::class,
            'storeLocation',
        ])->name('dashboard.admin.rute.lokasi.store');

        Route::get('/rute/jadwal', [
            AdminDashboardController::class,
            'scheduleIndex',
        ])->name('dashboard.admin.rute.jadwal');

        Route::post('/rute/jadwal', [
            AdminDashboardController::class,
            'storeSchedule',
        ])->name('dashboard.admin.rute.jadwal.store');

        Route::post('/rute/jadwal/batch-generate', [
            AdminDashboardController::class,
            'batchGenerateSchedules',
        ])->name('dashboard.admin.rute.jadwal.batch-generate');

        Route::patch('/rute/jadwal/{schedule}/assign', [
            AdminDashboardController::class,
            'assignScheduleVehicleDriver',
        ])->name('dashboard.admin.rute.jadwal.assign');

        Route::patch('/rute/jadwal/{schedule}/status', [
            AdminDashboardController::class,
            'updateScheduleStatus',
        ])->name('dashboard.admin.rute.jadwal.update-status');

        Route::delete('/rute/jadwal/{schedule}', [
            AdminDashboardController::class,
            'destroySchedule',
        ])->name('dashboard.admin.rute.jadwal.destroy');

        /*
        |--------------------------------------------------------------------------
        | Kelola Refund (Cancellation, Approval, Transparent Outflow, Seat Release)
        |--------------------------------------------------------------------------
        */
        Route::get('/refund', [
            AdminDashboardController::class,
            'refundIndex',
        ])->name('dashboard.admin.refund.index');

        Route::post('/refund', [
            AdminDashboardController::class,
            'storeRefund',
        ])->name('dashboard.admin.refund.store');

        Route::patch('/refund/{refund}/approve', [
            AdminDashboardController::class,
            'approveRefund',
        ])->name('dashboard.admin.refund.approve');

        Route::patch('/refund/{refund}/reject', [
            AdminDashboardController::class,
            'rejectRefund',
        ])->name('dashboard.admin.refund.reject');

        /*
        |--------------------------------------------------------------------------
        | Laporan (Rekonsiliasi Pembukuan, Keterisian Kursi, Manifes, & Refund)
        |--------------------------------------------------------------------------
        */
        Route::get('/laporan', [
            AdminDashboardController::class,
            'reportIndex',
        ])->name('dashboard.admin.laporan.index');

        Route::get('/laporan/export/excel', [
            AdminDashboardController::class,
            'exportReportExcel',
        ])->name('dashboard.admin.laporan.export.excel');

        Route::get('/laporan/export/pdf', [
            AdminDashboardController::class,
            'exportReportPdf',
        ])->name('dashboard.admin.laporan.export.pdf');

        /*
        |--------------------------------------------------------------------------
        | Pengaturan (Profil, Kebijakan, Pembayaran, Lokasi, Keamanan)
        |--------------------------------------------------------------------------
        */
        Route::get('/pengaturan', [
            AdminDashboardController::class,
            'settingIndex',
        ])->name('dashboard.admin.pengaturan.index');

        Route::post('/pengaturan/profil', [
            AdminDashboardController::class,
            'settingUpdateProfile',
        ])->name('dashboard.admin.pengaturan.profil');

        Route::post('/pengaturan/kebijakan', [
            AdminDashboardController::class,
            'settingUpdatePolicy',
        ])->name('dashboard.admin.pengaturan.kebijakan');

        Route::post('/pengaturan/pembayaran', [
            AdminDashboardController::class,
            'settingUpdatePayment',
        ])->name('dashboard.admin.pengaturan.pembayaran');

        Route::post('/pengaturan/lokasi', [
            AdminDashboardController::class,
            'settingStoreLocation',
        ])->name('dashboard.admin.pengaturan.lokasi.store');

        Route::put('/pengaturan/lokasi/{id}', [
            AdminDashboardController::class,
            'settingUpdateLocation',
        ])->name('dashboard.admin.pengaturan.lokasi.update');

        Route::delete('/pengaturan/lokasi/{id}', [
            AdminDashboardController::class,
            'settingDestroyLocation',
        ])->name('dashboard.admin.pengaturan.lokasi.destroy');

        Route::post('/pengaturan/keamanan', [
            AdminDashboardController::class,
            'settingUpdateSecurity',
        ])->name('dashboard.admin.pengaturan.keamanan');

    });

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])
    ->group(function () {

        Route::get('/admin/dashboard', [
            AdminBiasaDashboardController::class,
            'index',
        ])->name('dashboard.admin-biasa.index');

        Route::get('/admin/manajemen-booking', [
            AdminBiasaDashboardController::class,
            'manajemenBooking',
        ])->name('dashboard.admin-biasa.booking');

        Route::get('/admin/informasi-perjalanan', [
            AdminBiasaDashboardController::class,
            'informasiPerjalanan',
        ])->name('dashboard.admin-biasa.perjalanan');

        Route::get('/admin/validasi-tiket', [
            AdminBiasaDashboardController::class,
            'validasiTiket',
        ])->name('dashboard.admin-biasa.validasi-tiket');

        Route::post('/admin/validasi-tiket/validate', [
            AdminBiasaDashboardController::class,
            'ajaxValidateTicket',
        ])->name('dashboard.admin-biasa.validasi-tiket.validate');

        Route::patch('/admin/validasi-tiket/passenger/{passenger}/status', [
            AdminBiasaDashboardController::class,
            'ajaxUpdatePassengerBoardingStatus',
        ])->name('dashboard.admin-biasa.validasi-tiket.update-status');

    });

/*
|--------------------------------------------------------------------------
| Driver
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:driver'])
    ->group(function () {

        // 1. Dashboard Utama
        Route::get('/driver/dashboard', [
            DriverDashboardController::class,
            'index',
        ])->name('dashboard.driver.index');

        // 2. Melihat Jadwal Perjalanan
        Route::get('/driver/jadwal', [
            DriverDashboardController::class,
            'jadwal',
        ])->name('dashboard.driver.jadwal');

        // 3. Melihat Kendaraan yang Ditugaskan
        Route::get('/driver/kendaraan', [
            DriverDashboardController::class,
            'kendaraan',
        ])->name('dashboard.driver.kendaraan');

        // 4. Melihat Daftar Penumpang (Manifes)
        Route::get('/driver/daftar-penumpang', [
            DriverDashboardController::class,
            'daftarPenumpang',
        ])->name('dashboard.driver.penumpang');

        // 5. Melihat Seat Map
        Route::get('/driver/seat-map', [
            DriverDashboardController::class,
            'seatMap',
        ])->name('dashboard.driver.seatmap');

        // 6. Melihat Rute Perjalanan
        Route::get('/driver/rute', [
            DriverDashboardController::class,
            'rute',
        ])->name('dashboard.driver.rute');

        // 7. Melihat Status Perjalanan
        Route::get('/driver/status-perjalanan', [
            DriverDashboardController::class,
            'statusPerjalanan',
        ])->name('dashboard.driver.status');

        // 8. Mengubah Status Perjalanan
        Route::get('/driver/ubah-status', [
            DriverDashboardController::class,
            'ubahStatusPerjalanan',
        ])->name('dashboard.driver.ubah-status');

        // 9. Melihat Informasi Penumpang
        Route::get('/driver/info-penumpang', [
            DriverDashboardController::class,
            'infoPenumpang',
        ])->name('dashboard.driver.info-penumpang');

        // 10. Melaporkan Masalah Perjalanan
        Route::get('/driver/lapor-masalah', [
            DriverDashboardController::class,
            'laporMasalah',
        ])->name('dashboard.driver.lapor-masalah');

        // 11. Emergency Center
        Route::get('/driver/emergency', [
            DriverDashboardController::class,
            'emergencyCenter',
        ])->name('dashboard.driver.emergency');

        // 12. GPS Tracking Saat Perjalanan Aktif
        Route::get('/driver/gps-tracking', [
            DriverDashboardController::class,
            'gpsTracking',
        ])->name('dashboard.driver.gps');

        // 13. Melihat Performa
        Route::get('/driver/performa', [
            DriverDashboardController::class,
            'performa',
        ])->name('dashboard.driver.performa');

        // 14. Melihat Reward
        Route::get('/driver/reward', [
            DriverDashboardController::class,
            'reward',
        ])->name('dashboard.driver.reward');

        // 15. Melihat Riwayat Perjalanan
        Route::get('/driver/riwayat', [
            DriverDashboardController::class,
            'riwayat',
        ])->name('dashboard.driver.riwayat');

        // Action Routes
        Route::post('/driver/schedule/{schedule}/status', [
            DriverDashboardController::class,
            'updateTripStatus',
        ])->name('dashboard.driver.schedule.status');

        Route::post('/driver/schedule/{schedule}/gps', [
            DriverDashboardController::class,
            'updateGps',
        ])->name('dashboard.driver.schedule.gps');

        Route::patch('/driver/passenger/{passenger}/checkin', [
            DriverDashboardController::class,
            'updateCheckin',
        ])->name('dashboard.driver.passenger.checkin');

        Route::post('/driver/schedule/{schedule}/incident', [
            DriverDashboardController::class,
            'reportIncident',
        ])->name('dashboard.driver.schedule.incident');

        Route::post('/driver/schedule/{schedule}/sos', [
            DriverDashboardController::class,
            'triggerSos',
        ])->name('dashboard.driver.schedule.sos');

    });

/*
|--------------------------------------------------------------------------
| Customer Service
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:cs,customer_service'])
    ->group(function () {

        Route::get('/customer-service/dashboard', [
            CustomerServiceDashboardController::class,
            'index',
        ])->name('dashboard.customer-service.index');

        Route::get('/customer-service/live-chat', [
            CustomerServiceDashboardController::class,
            'liveChat',
        ])->name('dashboard.customer-service.live-chat');

        Route::get('/customer-service/booking-context', [
            CustomerServiceDashboardController::class,
            'bookingContext',
        ])->name('dashboard.customer-service.booking-context');

        Route::get('/customer-service/chat-history', [
            CustomerServiceDashboardController::class,
            'chatHistory',
        ])->name('dashboard.customer-service.chat-history');

        Route::get('/customer-service/send-file', [
            CustomerServiceDashboardController::class,
            'sendFile',
        ])->name('dashboard.customer-service.send-file');

        Route::get('/customer-service/escalation', [
            CustomerServiceDashboardController::class,
            'escalation',
        ])->name('dashboard.customer-service.escalation');

    });

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->group(function () {

        Route::get('/user/dashboard', [
            UserDashboardController::class,
            'index',
        ])->name('dashboard.user.index');

        Route::get('/booking/dashboard', [
            UserDashboardController::class,
            'index',
        ])->name('dashboard.user.user-booking');

        Route::get('/history/dashboard', [
            UserDashboardController::class,
            'index',
        ])->name('dashboard.user.user-history');

        Route::get('/refund/dashboard', [
            UserDashboardController::class,
            'index',
        ])->name('dashboard.user.user-refund');

        Route::get('/promo/dashboard', [
            UserDashboardController::class,
            'promo',
        ])->name('dashboard.user.user-promo');

        Route::post('/user/voucher/claim', [
            UserDashboardController::class,
            'claimVoucher',
        ])->name('dashboard.user.voucher.claim');

        Route::post('/user/penumpang-tersimpan', [
            UserDashboardController::class,
            'storeSavedPassenger',
        ])->name('dashboard.user.saved-passenger.store');

        Route::put('/user/penumpang-tersimpan/{savedPassenger}', [
            UserDashboardController::class,
            'updateSavedPassenger',
        ])->name('dashboard.user.saved-passenger.update');

        Route::delete('/user/penumpang-tersimpan/{savedPassenger}', [
            UserDashboardController::class,
            'destroySavedPassenger',
        ])->name('dashboard.user.saved-passenger.destroy');

        Route::post('/user/profil/update', [
            UserDashboardController::class,
            'updateProfile',
        ])->name('dashboard.user.profil.update');

        Route::post('/user/profil/password', [
            UserDashboardController::class,
            'updatePassword',
        ])->name('dashboard.user.profil.password');

        Route::get('/user/cari-jadwal', [
            UserDashboardController::class,
            'search',
        ])->name('dashboard.user.search');

        Route::get('/tiket/dashboard', [
            UserDashboardController::class,
            'tiket',
        ])->name('dashboard.user.user-tiket');

        Route::get('/notifikasi/dashboard', [
            UserDashboardController::class,
            'notifikasi',
        ])->name('dashboard.user.user-notifikasi');

        Route::get('/user/notifikasi/{id}/open', [
            UserDashboardController::class,
            'openNotification',
        ])->name('dashboard.user.notifikasi.open');

        Route::post('/user/notifikasi/mark-all-read', [
            UserDashboardController::class,
            'markAllAsRead',
        ])->name('dashboard.user.notifikasi.mark-all-read');

        Route::delete('/user/notifikasi/{id}', [
            UserDashboardController::class,
            'destroyNotification',
        ])->name('dashboard.user.notifikasi.destroy');

        Route::post('/user/notifikasi/simulate', [
            UserDashboardController::class,
            'simulateNotification',
        ])->name('dashboard.user.notifikasi.simulate');

        Route::get('/akun/dashboard', [
            UserDashboardController::class,
            'akun',
        ])->name('dashboard.user.user-akun');

        // Sub-Flow Tab Tiket Saya: Check-in, Pembatalan & Refund, Ulasan
        Route::post('/user/tiket/{ticket}/check-in', [
            UserDashboardController::class,
            'checkInTicket',
        ])->name('dashboard.user.tiket.check-in');

        Route::post('/user/tiket/{booking}/refund', [
            UserDashboardController::class,
            'submitRefund',
        ])->name('dashboard.user.tiket.refund');

        Route::post('/user/tiket/review/{booking}', [
            UserDashboardController::class,
            'submitReview',
        ])->name('dashboard.user.tiket.review');

    });

// API Midtrans Payment Initiation & Status
Route::post('/api/payment/charge', [UserDashboardController::class, 'apiChargePayment'])->name('api.payment.charge');
Route::post('/api/payment/create-token', [UserDashboardController::class, 'apiCreateSnapToken'])->name('api.payment.create-token');
Route::get('/api/payment/channels', [UserDashboardController::class, 'apiPaymentChannels'])->name('api.payment.channels');

// Webhook Payment Gateway Otomatis
Route::post('/api/payment/webhook', [UserDashboardController::class, 'paymentWebhook'])->name('payment.webhook');
Route::post('/payment/webhook', [UserDashboardController::class, 'paymentWebhook']);
