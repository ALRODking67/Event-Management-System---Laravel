
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel organisasi
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
        });

        // 2. Keanggotaan dan role per organisasi
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('role_id')
                ->constrained()
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(
                ['organization_id', 'user_id'],
                'org_member_unique'
            );
        });

        // 3. Undangan anggota organisasi
        Schema::create('organization_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('email');
            $table->foreignId('role_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('invited_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(
                ['organization_id', 'email'],
                'org_invitation_email_index'
            );
        });

        // 4. Tambahkan kepemilikan organisasi ke data utama EMS
        $tenantTables = [
            'venues',
            'equipment_categories',
            'equipment',
            'vendors',
            'events',
            'financial_transactions',
            'evaluations',
        ];

        foreach ($tenantTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('organization_id')
                    ->nullable()
                    ->constrained()
                    ->restrictOnDelete();
            });
        }

        // 5. Cari akun admin lama.
        // Jika tidak ada, gunakan pengguna pertama sebagai pembuat
        // organisasi awal.
        $admin = DB::table('users')
            ->where('email', 'admin@ems.id')
            ->first();

        if (!$admin) {
            $admin = DB::table('users')
                ->orderBy('id')
                ->first();
        }

        // Database pengujian dapat belum memiliki pengguna.
        // Dalam kondisi tersebut, organisasi awal tidak perlu dibuat.
        if (!$admin) {
            return;
        }

        // 6. Buat organisasi awal untuk menampung data lama
        $organizationId = DB::table('organizations')->insertGetId([
            'name' => 'Organisasi Awal EMS',
            'slug' => 'organisasi-awal-ems',
            'created_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Pindahkan role akun lama menjadi keanggotaan organisasi
        $users = DB::table('users')
            ->whereNotNull('role_id')
            ->get();

        foreach ($users as $user) {
            DB::table('organization_members')->insert([
                'organization_id' => $organizationId,
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Hubungkan data EMS lama ke organisasi awal
        foreach ([
            'venues',
            'equipment_categories',
            'equipment',
            'vendors',
            'events',
        ] as $tableName) {
            DB::table($tableName)->update([
                'organization_id' => $organizationId,
            ]);
        }

        // 9. Data keuangan mengikuti organisasi acara.
        // Menggunakan subquery agar kompatibel dengan MySQL dan SQLite.
        DB::table('financial_transactions')->update([
            'organization_id' => DB::raw(
                '(SELECT organization_id
                  FROM events
                  WHERE events.id = financial_transactions.event_id)'
            ),
        ]);

        // 10. Evaluasi mengikuti organisasi acara.
        DB::table('evaluations')->update([
            'organization_id' => DB::raw(
                '(SELECT organization_id
                  FROM events
                  WHERE events.id = evaluations.event_id)'
            ),
        ]);
    }

    public function down(): void
    {
        foreach ([
            'evaluations',
            'financial_transactions',
            'events',
            'vendors',
            'equipment',
            'equipment_categories',
            'venues',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('organization_id');
            });
        }

        Schema::dropIfExists('organization_invitations');
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organizations');
    }
};
