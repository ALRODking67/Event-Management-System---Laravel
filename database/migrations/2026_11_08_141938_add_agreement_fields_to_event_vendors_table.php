<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_vendors', function (Blueprint $table) {
            $table->string('agreement_status')
                ->default('negotiating')
                ->after('assignment_status');

            $table->dateTime('agreed_start_datetime')
                ->nullable()
                ->after('agreement_status');

            $table->dateTime('agreed_end_datetime')
                ->nullable()
                ->after('agreed_start_datetime');

            $table->text('agreed_scope')
                ->nullable()
                ->after('agreed_end_datetime');

            $table->text('agreed_payment_terms')
                ->nullable()
                ->after('agreed_scope');

            $table->timestamp('agreement_accepted_at')
                ->nullable()
                ->after('agreed_payment_terms');

            $table->foreignId('agreement_accepted_by')
                ->nullable()
                ->after('agreement_accepted_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_vendors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agreement_accepted_by');

            $table->dropColumn([
                'agreement_status',
                'agreed_start_datetime',
                'agreed_end_datetime',
                'agreed_scope',
                'agreed_payment_terms',
                'agreement_accepted_at',
            ]);
        });
    }
};