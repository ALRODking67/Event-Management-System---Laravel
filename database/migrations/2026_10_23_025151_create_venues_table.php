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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('venue_name', 150);
            $table->text('address')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->text('electricity_info')->nullable();
            $table->text('access_info')->nullable();
            $table->text('security_info')->nullable();
            $table->text('parking_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
