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
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                </svg>
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
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
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
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
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
                                            <div class="alert-item-code" style="display: flex; align-items: center; gap: 6px;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h13l4 4v7H3V6z"/>
                                                    <circle cx="7.5" cy="17.5" r="2"/>
                                                    <circle cx="16.5" cy="17.5" r="2"/>
                                                    <path d="M16 6v4h4"/>
                                                </svg>
                                                <span>Uji KIR: {{ $vExp->name }} ({{ $vExp->plate_number }})</span>
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
                                            <div class="alert-item-code" style="display: flex; align-items: center; gap: 6px;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                <span>Pajak STNK: {{ $vExp->name }} ({{ $vExp->plate_number }})</span>
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
                                        <div class="alert-item-code" style="display: flex; align-items: center; gap: 6px;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                                <circle cx="9" cy="10" r="2"/>
                                                <path d="M15 8h2"/>
                                                <path d="M15 12h2"/>
                                            </svg>
                                            <span>SIM Driver: {{ $drv->user?->name ?? 'Driver' }}</span>
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
                                        <div class="alert-item-code" style="display: flex; align-items: center; gap: 6px;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h13l4 4v7H3V6z"/>
                                                <circle cx="7.5" cy="17.5" r="2"/>
                                                <circle cx="16.5" cy="17.5" r="2"/>
                                                <path d="M16 6v4h4"/>
                                            </svg>
                                            <span>{{ $vh->name }} ({{ $vh->plate_number }})</span>
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
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h13l4 4v7H3V6z"/>
                                    <circle cx="7.5" cy="17.5" r="2"/>
                                    <circle cx="16.5" cy="17.5" r="2"/>
                                    <path d="M16 6v4h4"/>
                                </svg>
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
