<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_routes', function (Blueprint $table) {
            $table->id();

            $table->string('origin');

            $table->string('destination');

            $table->string('slug')
                ->unique();

            $table->decimal('price', 12, 2);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->index([
                'origin',
                'destination',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_routes');
    }
};
