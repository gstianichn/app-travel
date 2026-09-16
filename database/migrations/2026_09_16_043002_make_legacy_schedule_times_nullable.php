<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('departure_at')->nullable()->change();
            $table->dateTime('arrival_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('departure_at')->nullable(false)->change();
            $table->dateTime('arrival_at')->nullable(false)->change();
        });
    }
};