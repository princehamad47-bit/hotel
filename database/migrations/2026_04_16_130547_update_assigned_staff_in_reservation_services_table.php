<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_services', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_services', 'assigned_user_id')) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropColumn('assigned_user_id');
            }

            if (!Schema::hasColumn('reservation_services', 'assigned_staff_id')) {
                $table->foreignId('assigned_staff_id')
                    ->nullable()
                    ->after('service_id')
                    ->constrained('staff')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservation_services', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_services', 'assigned_staff_id')) {
                $table->dropForeign(['assigned_staff_id']);
                $table->dropColumn('assigned_staff_id');
            }

            if (!Schema::hasColumn('reservation_services', 'assigned_user_id')) {
                $table->foreignId('assigned_user_id')
                    ->nullable()
                    ->after('service_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }
};
