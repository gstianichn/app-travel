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
        Schema::table('refunds', function (Blueprint $table) {
            $table->decimal('original_amount', 12, 2)->nullable()->after('user_id');
            $table->decimal('cancellation_fee', 12, 2)->default(0)->after('amount');
            $table->string('bank_name', 50)->nullable()->after('reason');
            $table->string('account_number', 50)->nullable()->after('bank_name');
            $table->string('account_holder', 100)->nullable()->after('account_number');
            $table->string('transfer_proof', 255)->nullable()->after('account_holder');
            $table->foreignId('processed_by')->nullable()->after('processed_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('processed_by');
            $table->dropColumn([
                'original_amount',
                'cancellation_fee',
                'bank_name',
                'account_number',
                'account_holder',
                'transfer_proof',
            ]);
        });
    }
};
