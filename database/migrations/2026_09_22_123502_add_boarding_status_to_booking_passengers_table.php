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
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->string('boarding_status')->default('belum_hadir')->after('special_request');
            $table->timestamp('boarded_at')->nullable()->after('boarding_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->dropColumn(['boarding_status', 'boarded_at']);
        });
    }
};
