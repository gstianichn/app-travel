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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('source')
                ->default('online')
                ->after('status');

            $table->text('notes')
                ->nullable()
                ->after('source');

            $table->timestamp('rescheduled_at')
                ->nullable()
                ->after('notes');

            $table->string('reschedule_reason')
                ->nullable()
                ->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'notes',
                'rescheduled_at',
                'reschedule_reason',
            ]);
        });
    }
};
