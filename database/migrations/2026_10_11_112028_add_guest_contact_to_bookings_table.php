<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Passengers may book without an account, so a booking keeps its own contact
     * details and only optionally belongs to a user.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            $table->string('contact_name')->nullable()->after('user_id');
            $table->string('contact_phone', 25)->nullable()->after('contact_name');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'contact_email']);
        });
    }
};
