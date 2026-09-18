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
        Schema::table('locations', function (Blueprint $table) {
            $table->string('city')
                ->nullable()
                ->after('code');

            $table->text('address')
                ->nullable()
                ->after('city');

            $table->string('phone')
                ->nullable()
                ->after('address');

            $table->string('type')
                ->default('pool')
                ->after('phone');

            $table->boolean('is_active')
                ->default(true)
                ->after('type');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->integer('distance_km')
                ->nullable()
                ->after('duration');

            $table->decimal('weekend_price', 12, 2)
                ->nullable()
                ->after('price');

            $table->decimal('holiday_price', 12, 2)
                ->nullable()
                ->after('weekend_price');

            $table->text('notes')
                ->nullable()
                ->after('distance_km');

            $table->boolean('is_active')
                ->default(true)
                ->after('notes');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->decimal('price', 12, 2)
                ->nullable()
                ->after('available_seats');

            $table->text('notes')
                ->nullable()
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['price', 'notes']);
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn([
                'distance_km',
                'weekend_price',
                'holiday_price',
                'notes',
                'is_active',
            ]);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'address',
                'phone',
                'type',
                'is_active',
            ]);
        });
    }
};
