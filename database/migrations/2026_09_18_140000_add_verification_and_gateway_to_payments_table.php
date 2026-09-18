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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_image')
                ->nullable()
                ->after('payment_method');

            $table->string('bank_name')
                ->nullable()
                ->after('proof_image');

            $table->string('account_name')
                ->nullable()
                ->after('bank_name');

            $table->string('account_number')
                ->nullable()
                ->after('account_name');

            $table->text('notes')
                ->nullable()
                ->after('account_number');

            $table->foreignId('verified_by')
                ->nullable()
                ->after('notes')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verified_by');

            $table->string('gateway_provider')
                ->nullable()
                ->after('verified_at');

            $table->string('gateway_status')
                ->nullable()
                ->after('gateway_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'proof_image',
                'bank_name',
                'account_name',
                'account_number',
                'notes',
                'verified_by',
                'verified_at',
                'gateway_provider',
                'gateway_status',
            ]);
        });
    }
};
