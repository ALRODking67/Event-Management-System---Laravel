<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_vendor_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_vendor_id')
                ->constrained('event_vendors')
                ->cascadeOnDelete();

            $table->decimal('amount', 14, 2);

            $table->string('payment_type')->default('Installment');
            $table->date('payment_date');

            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['event_vendor_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_vendor_payments');
    }
};