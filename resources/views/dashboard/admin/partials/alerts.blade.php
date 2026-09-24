{{-- =========================================
         3. NOTIFIKASI PENTING
    ========================================== --}}
    <div>
        <div class="section-header-wrap">
            <div>
                <h2>Notifikasi & Pengingat Penting</h2>
                <p>Tindak lanjut pengajuan refund dan pengingat kelayakan armada serta pengemudi</p>
            </div>
        </div>

        <div class="alerts-section" style="margin-top: 14px;">

            {{-- PENGINGAT REFUND BELUM DIPROSES --}}
            @if(isset($pendingRefunds) && $pendingRefunds->count() > 0)
                <div class="alert-card refund-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                🔄
                            </div>
                            <div>
                                <h3>Persetujuan Refund Menunggu</h3>
                                <p>Terdapat permintaan refund yang memerlukan tindakan verifikasi admin</p>
                            </div>
                        </div>
                        <span class="alert-badge-count">
                            {{ $totalPendingRefunds ?? $pendingRefunds->count() }} Menunggu
                        </span>
                    </div>

                    <div class="alert-item-list">
                        @foreach($pendingRefunds as $rf)
                            <div class="alert-item">
                                <div class="alert-item-main">
                                    <div class="alert-item-code">
                                        {{ $rf->booking?->booking_code ?? 'Booking #' . $rf->booking_id }}
                                        &bull; Rp {{ number_format($rf->amount, 0, ',', '.') }}
                                    </div>
                                    <div class="alert-item-sub">
                                        Pemohon: {{ $rf->user?->name ?? 'Pelanggan' }} &bull; {{ $rf->reason ? Str::limit($rf->reason, 35) : 'Pengajuan refund' }}
                                    </div>
                                </div>
                                <div>
                                    <span style="font-size: 11px; color: #b45309; font-weight: 700;">
                                        {{ $rf->created_at ? $rf->created_at->diffForHumans() : 'Baru' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert-card-footer">
                        <a href="{{ route('dashboard.admin.refund.index') }}" class="alert-footer-link">
                            Buka Modul Refund &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="alert-card clean-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                ✓
                            </div>
                            <div>
                                <h3>Tidak Ada Refund Tertunda</h3>
                                <p>Seluruh permohonan pengembalian dana telah diproses dengan tuntas.</p>
                            </div>
                        </div>
                        <span class="metric-badge-status green">Terkendali</span>
                    </div>
                    <div class="alert-item" style="border: none; background: transparent; padding-left: 0;">
                        <span style="color: #61736b; font-size: 12px;">
                            Sistem akan otomatis memberi tahu Anda jika ada pelanggan yang mengajukan refund baru.
                        </span>
                    </div>
                </div>
            @endif

            {{-- PENGINGAT UJI KIR / PAJAK ARMADA & SIM DRIVER --}}
            @php
                $hasMaintenance = isset($maintenanceVehicles) && $maintenanceVehicles->count() > 0;
                $hasExpiringDrivers = isset($expiringDrivers) && $expiringDrivers->count() > 0;
                $hasExpiringVehicles = isset($expiringVehicles) && $expiringVehicles->count() > 0;
            @endphp

            @if($hasMaintenance || $hasExpiringDrivers || $hasExpiringVehicles)
                <div class="alert-card maintenance-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                ⚠️
                            </div>
                            <div>
                                <h3>Perhatian Kelayakan Armada & Driver</h3>
                                <p>Pengingat uji KIR armada, jatuh tempo pajak STNK, dan lisensi SIM driver</p>
                            </div>
                        </div>
                        <span class="alert-badge-count">
                            Perlu Cek
                        </span>
                    </div>

                    <div class="alert-item-list">
                        {{-- VEHICLES EXPIRING KIR / STNK --}}
                        @if($hasExpiringVehicles)
                            @foreach($expiringVehicles as $vExp)
                                @php
                                    $kirInfo = $vExp->kirStatus();
                                    $stnkInfo = $vExp->stnkStatus();
                                @endphp
                                @if($kirInfo['status'] === 'expired' || $kirInfo['status'] === 'expiring_soon')
                                    <div class="alert-item">
                                        <div class="alert-item-main">
                                            <div class="alert-item-code">
                                                🚐 Uji KIR: {{ $vExp->name }} ({{ $vExp->plate_number }})
                                            </div>
                                            <div class="alert-item-sub">
                                                Masa Berlaku: {{ $vExp->kir_expiry_date ? $vExp->kir_expiry_date->format('d/m/Y') : '-' }} &bull; Tipe: {{ $vExp->type }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($kirInfo['status'] === 'expired')
                                                <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                    KIR Kedaluwarsa
                                                </span>
                                            @else
                                                <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                    KIR Sisa {{ $kirInfo['days_left'] }} Hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($stnkInfo['status'] === 'expired' || $stnkInfo['status'] === 'expiring_soon')
                                    <div class="alert-item">
                                        <div class="alert-item-main">
                                            <div class="alert-item-code">
                                                📄 Pajak STNK: {{ $vExp->name }} ({{ $vExp->plate_number }})
                                            </div>
                                            <div class="alert-item-sub">
                                                Jatuh Tempo: {{ $vExp->stnk_tax_due_date ? $vExp->stnk_tax_due_date->format('d/m/Y') : '-' }} &bull; Tipe: {{ $vExp->type }}
                                            </div>
                                        </div>
                                        <div>
                                            @if($stnkInfo['status'] === 'expired')
                                                <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                    Pajak Lewat
                                                </span>
                                            @else
                                                <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                    Pajak Sisa {{ $stnkInfo['days_left'] }} Hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- DRIVERS EXPIRING --}}
                        @if($hasExpiringDrivers)
                            @foreach($expiringDrivers as $drv)
                                @php
                                    $daysRemaining = now()->diffInDays($drv->license_expired_at, false);
                                @endphp
                                <div class="alert-item">
                                    <div class="alert-item-main">
                                        <div class="alert-item-code">
                                            🪪 SIM Driver: {{ $drv->user?->name ?? 'Driver' }}
                                        </div>
                                        <div class="alert-item-sub">
                                            No. Lisensi: {{ $drv->license_number }} &bull; Jatuh tempo: {{ $drv->license_expired_at ? $drv->license_expired_at->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                    <div>
                                        @if($daysRemaining < 0)
                                            <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                                Kedaluwarsa
                                            </span>
                                        @else
                                            <span style="font-size: 10px; font-weight: 700; color: #d97706; background: #fef3c7; padding: 3px 8px; border-radius: 6px;">
                                                Tersisa {{ $daysRemaining }} Hari
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- VEHICLES MAINTENANCE --}}
                        @if($hasMaintenance)
                            @foreach($maintenanceVehicles as $vh)
                                <div class="alert-item">
                                    <div class="alert-item-main">
                                        <div class="alert-item-code">
                                            🚐 {{ $vh->name }} ({{ $vh->plate_number }})
                                        </div>
                                        <div class="alert-item-sub">
                                            Status: {{ $vh->status }} &bull; Kapasitas {{ $vh->capacity }} Kursi
                                        </div>
                                    </div>
                                    <div>
                                        <span style="font-size: 10px; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 3px 8px; border-radius: 6px;">
                                            Masuk Bengkel
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="alert-card-footer">
                        <a href="{{ route('dashboard.admin.memantau-kendaraan.index') }}" class="alert-footer-link">
                            Pantau Kelayakan Armada &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="alert-card clean-alert">
                    <div class="alert-card-header">
                        <div class="alert-card-title">
                            <div class="alert-icon-pill">
                                🚐
                            </div>
                            <div>
                                <h3>Armada & Driver Dalam Kondisi Siap</h3>
                                <p>Semua lisensi SIM driver aktif dan seluruh armada dalam status layak operasi.</p>
                            </div>
                        </div>
                        <span class="metric-badge-status green">KIR & SIM Aman</span>
                    </div>
                    <div class="alert-item" style="border: none; background: transparent; padding-left: 0;">
                        <span style="color: #61736b; font-size: 12px;">
                            Tidak ada pengingat kedaluwarsa dokumen kendaraan atau driver dalam 30 hari ke depan.
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </div>
