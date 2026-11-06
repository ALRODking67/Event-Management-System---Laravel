<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat unique index hanya jika kolom vendor_id sudah tersedia.
        if (Schema::hasColumn('users', 'vendor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('vendor_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'vendor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['vendor_id']);
            });
        }
    }
};