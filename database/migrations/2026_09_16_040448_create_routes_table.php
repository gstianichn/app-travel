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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('origin_id')
                ->constrained('locations')
                ->cascadeOnDelete();

            $table->foreignId('destination_id')
                ->constrained('locations')
                ->cascadeOnDelete();

            $table->decimal('price', 12, 2);

            $table->string('duration');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
