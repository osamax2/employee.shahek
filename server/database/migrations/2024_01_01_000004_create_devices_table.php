<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->string('device_id')->unique();
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_manufacturer')->nullable();
            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            
            $table->index('device_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
