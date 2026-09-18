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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
            if (! Schema::hasColumn('users', 'branch')) {
                $table->string('branch')->nullable()->after('status');
            }
            if (! Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('branch');
            }
            if (! Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'branch')) {
                $columnsToDrop[] = 'branch';
            }
            if (Schema::hasColumn('users', 'position')) {
                $columnsToDrop[] = 'position';
            }
            if (Schema::hasColumn('users', 'permissions')) {
                $columnsToDrop[] = 'permissions';
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
