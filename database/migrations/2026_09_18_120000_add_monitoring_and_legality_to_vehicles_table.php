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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('stnk_tax_due_date')
                ->nullable()
                ->after('status');

            $table->date('kir_expiry_date')
                ->nullable()
                ->after('stnk_tax_due_date');

            $table->unsignedSmallInteger('manufacture_year')
                ->nullable()
                ->after('kir_expiry_date');

            $table->string('chassis_number')
                ->nullable()
                ->after('manufacture_year');

            $table->string('engine_number')
                ->nullable()
                ->after('chassis_number');

            $table->text('notes')
                ->nullable()
                ->after('engine_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'stnk_tax_due_date',
                'kir_expiry_date',
                'manufacture_year',
                'chassis_number',
                'engine_number',
                'notes',
            ]);
        });
    }
};
