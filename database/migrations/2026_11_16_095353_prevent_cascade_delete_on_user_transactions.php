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
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('bookings', function (Blueprint $table) use ($driver) {
            $table->foreignId('user_id')->nullable()->change();
            if ($driver !== 'sqlite') {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        Schema::table('refunds', function (Blueprint $table) use ($driver) {
            $table->foreignId('user_id')->nullable()->change();
            if ($driver !== 'sqlite') {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('bookings', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });

        Schema::table('refunds', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->dropForeign(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }
};
