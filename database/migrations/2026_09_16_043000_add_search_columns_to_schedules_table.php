<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('route_id')
                ->nullable()
                ->after('id')
                ->constrained('routes')
                ->cascadeOnDelete();
            $table->date('departure_date')->nullable()->after('route_id');
            $table->time('departure_time')->nullable()->after('departure_date');
            $table->unsignedInteger('available_seats')->nullable()->after('departure_time');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['route_id']);
            $table->dropColumn([
                'route_id',
                'departure_date',
                'departure_time',
                'available_seats',
            ]);
        });
    }
};
