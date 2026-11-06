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
        Schema::create('event_vendors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->foreignId('vendor_id')
                ->constrained('vendors')
                ->restrictOnDelete();

            $table->text('service_description');
            $table->decimal('agreed_cost', 14, 2)->default(0);
            $table->string('assignment_status', 30)->default('Requested');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_vendors');
    }
};
