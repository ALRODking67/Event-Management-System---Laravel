
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_members', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('role_id')
                ->constrained('vendors')
                ->nullOnDelete();

            $table->unique(
                ['organization_id', 'vendor_id'],
                'org_member_vendor_unique'
            );
        });

        // Pindahkan hubungan Vendor lama ke keanggotaan organisasi.
        // Menggunakan Query Builder agar kompatibel dengan MySQL dan SQLite.
        $members = DB::table('organization_members as om')
            ->join('users as u', 'u.id', '=', 'om.user_id')
            ->join('vendors as v', function ($join) {
                $join->on('v.id', '=', 'u.vendor_id')
                    ->on('v.organization_id', '=', 'om.organization_id');
            })
            ->join('roles as r', 'r.id', '=', 'om.role_id')
            ->where('r.role_name', 'Vendor')
            ->whereNotNull('u.vendor_id')
            ->select('om.id', 'u.vendor_id')
            ->get();

        foreach ($members as $member) {
            DB::table('organization_members')
                ->where('id', $member->id)
                ->update([
                    'vendor_id' => $member->vendor_id,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('organization_members', function (Blueprint $table) {
            $table->dropUnique('org_member_vendor_unique');
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
};
