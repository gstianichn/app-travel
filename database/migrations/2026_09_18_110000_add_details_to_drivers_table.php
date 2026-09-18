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
        Schema::table('drivers', function (Blueprint $table) {
            if (! Schema::hasColumn('drivers', 'license_type')) {
                $table->string('license_type')->nullable()->after('license_number');
            }

            if (! Schema::hasColumn('drivers', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('license_expired_at');
            }

            if (! Schema::hasColumn('drivers', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }

            if (! Schema::hasColumn('drivers', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            }

            if (! Schema::hasColumn('drivers', 'address')) {
                $table->text('address')->nullable()->after('emergency_contact_relation');
            }

            if (! Schema::hasColumn('drivers', 'notes')) {
                $table->text('notes')->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'license_type',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'address',
                'notes',
            ]);
        });
    }
};
