<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat kolom dan unique index jika belum tersedia.
        if (!Schema::hasColumn('users', 'vendor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('vendor_id')
                    ->nullable()
                    ->after('role_id')
                    ->constrained('vendors')
                    ->nullOnDelete();

                $table->unique('vendor_id');
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus kolom atau index.
        // Data MySQL yang sudah ada harus tetap aman.
    }
};