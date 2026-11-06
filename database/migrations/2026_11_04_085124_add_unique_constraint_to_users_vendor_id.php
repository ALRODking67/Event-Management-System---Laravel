
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Unique index vendor_id sudah dibuat oleh migration sebelumnya.
    }

    public function down(): void
    {
        // Tidak ada perubahan yang perlu dibatalkan.
    }
};
