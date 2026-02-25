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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('aksi');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_tabel')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->json('data_lama')->nullable()->comment('Data sebelum perubahan');
            $table->json('data_baru')->nullable()->comment('Data setelah perubahan');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Index untuk query yang lebih cepat
            $table->index(['user_id', 'waktu']);
            $table->index(['target_tabel', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
