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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('equipment_categories')
                ->restrictOnDelete();

            $table->string('equipment_name', 150);
            $table->string('asset_code', 80)->unique();
            $table->string('condition_status', 30)->default('Good');
            $table->string('availability_status', 30)->default('Available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
