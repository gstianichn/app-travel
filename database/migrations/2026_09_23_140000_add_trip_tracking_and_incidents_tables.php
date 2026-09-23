<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kolom pelacakan operasional & GPS pada tabel schedules
        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'trip_status')) {
                $table->string('trip_status')->default('scheduled')->after('status');
            }
            if (! Schema::hasColumn('schedules', 'pickup_started_at')) {
                $table->timestamp('pickup_started_at')->nullable()->after('trip_status');
            }
            if (! Schema::hasColumn('schedules', 'departed_at')) {
                $table->timestamp('departed_at')->nullable()->after('pickup_started_at');
            }
            if (! Schema::hasColumn('schedules', 'arrived_at')) {
                $table->timestamp('arrived_at')->nullable()->after('departed_at');
            }
            if (! Schema::hasColumn('schedules', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('arrived_at');
            }
            if (! Schema::hasColumn('schedules', 'current_latitude')) {
                $table->decimal('current_latitude', 10, 7)->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('schedules', 'current_longitude')) {
                $table->decimal('current_longitude', 10, 7)->nullable()->after('current_latitude');
            }
            if (! Schema::hasColumn('schedules', 'last_gps_at')) {
                $table->timestamp('last_gps_at')->nullable()->after('current_longitude');
            }
        });

        // 2. Kolom titik jemput, titik antar, dan catatan bagasi pada booking_passengers
        Schema::table('booking_passengers', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_passengers', 'pickup_point')) {
                $table->string('pickup_point')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('booking_passengers', 'dropoff_point')) {
                $table->string('dropoff_point')->nullable()->after('pickup_point');
            }
            if (! Schema::hasColumn('booking_passengers', 'baggage_notes')) {
                $table->string('baggage_notes')->nullable()->after('dropoff_point');
            }
        });

        // 3. Tabel insiden / kendala di jalan dan tombol darurat SOS
        if (! Schema::hasTable('trip_incidents')) {
            Schema::create('trip_incidents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')
                    ->nullable()
                    ->constrained('schedules')
                    ->nullOnDelete();
                $table->foreignId('driver_id')
                    ->constrained('drivers')
                    ->cascadeOnDelete();
                $table->string('category'); // ban_bocor, macet_parah, mesin_mogok, penumpang_batal, kecelakaan, darurat_medis, sos_emergency, lainnya
                $table->string('severity')->default('sedang'); // rendah, sedang, tinggi, darurat
                $table->text('description');
                $table->string('location_name')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('status')->default('reported'); // reported, in_progress, resolved
                $table->boolean('emergency_dispatched')->default(false);
                $table->timestamp('reported_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->timestamps();

                $table->index(['schedule_id', 'driver_id']);
                $table->index('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_incidents');

        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->dropColumn(['pickup_point', 'dropoff_point', 'baggage_notes']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'trip_status',
                'pickup_started_at',
                'departed_at',
                'arrived_at',
                'completed_at',
                'current_latitude',
                'current_longitude',
                'last_gps_at',
            ]);
        });
    }
};
