<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('reservations', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'checked_in_at')) {
                $table->dropColumn('checked_in_at');
            }

            if (Schema::hasColumn('reservations', 'checked_out_at')) {
                $table->dropColumn('checked_out_at');
            }
        });
    }
};
