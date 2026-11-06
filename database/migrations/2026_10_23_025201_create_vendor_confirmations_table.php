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
        Schema::create('vendor_confirmations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_vendor_id')
                ->constrained('event_vendors')
                ->cascadeOnDelete();

            $table->string('availability_status', 20);
            $table->dateTime('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_confirmations');
    }
};
