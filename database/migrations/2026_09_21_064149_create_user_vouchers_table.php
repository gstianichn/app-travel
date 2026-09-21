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
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('title');
            $table->string('discount_type', 20)->default('percent'); // percent, fixed
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_transaction', 10, 2)->default(0);
            $table->string('applicable_route')->nullable(); // misal 'Bandung - Jakarta' atau null untuk semua
            $table->string('source', 30)->default('claim_promo'); // claim_promo, compensation, welcome_gift
            $table->string('status', 20)->default('active'); // active, used, expired
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};
