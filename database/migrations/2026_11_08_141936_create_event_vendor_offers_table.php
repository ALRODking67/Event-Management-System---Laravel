<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_vendor_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_vendor_id')
                ->constrained('event_vendors')
                ->cascadeOnDelete();

            $table->foreignId('offered_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('offer_type')->default('initial');
            $table->decimal('amount', 14, 2)->nullable();

            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();

            $table->text('scope_description')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('Pending');

            $table->foreignId('responded_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('responded_at')->nullable();

            $table->foreignId('accepted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->index(['event_vendor_id', 'created_at']);
            $table->index(['event_vendor_id', 'status']);
        });

        /*
         * Pindahkan penawaran lama menjadi riwayat penawaran awal.
         * Nilai lama tidak langsung dianggap sebagai kesepakatan.
         */
        $fallbackUserId = DB::table('users')->value('id');

        $assignments = DB::table('event_vendors')
            ->leftJoin('events', 'event_vendors.event_id', '=', 'events.id')
            ->leftJoin('organizations', 'events.organization_id', '=', 'organizations.id')
            ->select(
                'event_vendors.*',
                'events.start_datetime',
                'events.end_datetime',
                'events.created_by as event_creator_id',
                'organizations.created_by as organization_creator_id'
            )
            ->get();

        foreach ($assignments as $assignment) {
            $userId = $assignment->event_creator_id
                ?? $assignment->organization_creator_id
                ?? $fallbackUserId;

            if (!$userId) {
                continue;
            }

            DB::table('event_vendor_offers')->insert([
                'event_vendor_id' => $assignment->id,
                'offered_by_user_id' => $userId,
                'offer_type' => 'initial',
                'amount' => $assignment->agreed_cost,
                'start_datetime' => $assignment->start_datetime,
                'end_datetime' => $assignment->end_datetime,
                'scope_description' => $assignment->service_description,
                'payment_terms' => null,
                'notes' => 'Penawaran awal yang dipindahkan dari data penugasan vendor sebelumnya.',
                'status' => 'Pending',
                'created_at' => $assignment->created_at ?? now(),
                'updated_at' => $assignment->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_vendor_offers');
    }
};