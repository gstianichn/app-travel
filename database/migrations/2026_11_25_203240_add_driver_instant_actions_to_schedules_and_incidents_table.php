<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'departing_at')) {
                $table->timestamp('departing_at')->nullable()->after('trip_status');
            }
            if (! Schema::hasColumn('schedules', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('schedules', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('schedules', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('trip_incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('trip_incidents', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->after('driver_id')->constrained('vehicles')->nullOnDelete();
            }
            if (! Schema::hasColumn('trip_incidents', 'needs_replacement_vehicle')) {
                $table->boolean('needs_replacement_vehicle')->default(false)->after('emergency_dispatched');
            }
            if (! Schema::hasColumn('trip_incidents', 'activated_at')) {
                $table->timestamp('activated_at')->nullable()->after('reported_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trip_incidents', function (Blueprint $table) {
            if (Schema::hasColumn('trip_incidents', 'vehicle_id')) {
                $table->dropConstrainedForeignId('vehicle_id');
            }
            if (Schema::hasColumn('trip_incidents', 'needs_replacement_vehicle')) {
                $table->dropColumn('needs_replacement_vehicle');
            }
            if (Schema::hasColumn('trip_incidents', 'activated_at')) {
                $table->dropColumn('activated_at');
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'cancelled_by')) {
                $table->dropConstrainedForeignId('cancelled_by');
            }
            if (Schema::hasColumn('schedules', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('schedules', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (Schema::hasColumn('schedules', 'departing_at')) {
                $table->dropColumn('departing_at');
            }
        });
    }
};
