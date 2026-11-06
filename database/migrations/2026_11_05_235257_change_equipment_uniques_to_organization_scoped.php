<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori: hapus keunikan global.
        Schema::table('equipment_categories', function (Blueprint $table) {
            $table->dropUnique('equipment_categories_category_name_unique');

            $table->unique(
                ['organization_id', 'category_name'],
                'equipment_categories_org_category_unique'
            );
        });

        // Kode aset: hapus keunikan global.
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropUnique('equipment_asset_code_unique');

            $table->unique(
                ['organization_id', 'asset_code'],
                'equipment_org_asset_unique'
            );
        });
    }

    public function down(): void
    {
        // Periksa apakah rollback ke aturan unik global masih aman.
        $duplicateCategory = DB::table('equipment_categories')
            ->select('category_name')
            ->groupBy('category_name')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        $duplicateAsset = DB::table('equipment')
            ->select('asset_code')
            ->groupBy('asset_code')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateCategory || $duplicateAsset) {
            throw new RuntimeException(
                'Rollback tidak dapat dilakukan karena terdapat nama kategori atau kode aset yang sama di organisasi berbeda.'
            );
        }

        Schema::table('equipment_categories', function (Blueprint $table) {
            $table->dropUnique('equipment_categories_org_category_unique');

            $table->unique('category_name', 'equipment_categories_category_name_unique');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropUnique('equipment_org_asset_unique');

            $table->unique('asset_code', 'equipment_asset_code_unique');
        });
    }
};