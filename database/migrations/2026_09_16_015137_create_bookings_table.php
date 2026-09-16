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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code')
                ->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->restrictOnDelete();

            $table->unsignedInteger('total_passenger');

            $table->decimal('subtotal', 12, 2);

            $table->decimal('discount', 12, 2)
                ->default(0);

            $table->decimal('total_price', 12, 2);

            $table->string('status')
                ->default('Pending');

            $table->timestamp('expired_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
