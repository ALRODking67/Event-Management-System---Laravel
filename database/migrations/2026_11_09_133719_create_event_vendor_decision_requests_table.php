<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_vendor_decision_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_vendor_id')
                ->constrained('event_vendors')
                ->cascadeOnDelete();

            $table->foreignId('event_vendor_offer_id')
                ->constrained('event_vendor_offers')
                ->restrictOnDelete();

            $table->foreignId('requested_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('requested_decision')->default('accept');
            $table->string('status')->default('Pending');

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index(['event_vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_vendor_decision_requests');
    }
};