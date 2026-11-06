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
        Schema::create('event_concepts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->unique()
                ->constrained('events')
                ->cascadeOnDelete();

            $table->text('purpose')->nullable();
            $table->string('theme', 150);
            $table->text('target_audience')->nullable();
            $table->string('event_format', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_concepts');
    }
};
