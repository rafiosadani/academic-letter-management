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
        Schema::create('notifications', function (Blueprint $table) {
            // Laravel Standard
            $table->uuid('id')->primary();
            $table->string('type')->comment('Nama kelas notifikasi yg digunakan');
            $table->morphs('notifiable');
            $table->json('data')->comment('Data fleksibel notifikasi dalam format JSON');
            $table->timestamp('read_at')->nullable()->comment('Waktu notifikasi dibaca oleh pengguna');

            // Custom additions for better querying
            $table->string('category', 50)->nullable()->comment('Kategori notifikasi (misalnya: academic_year, semester, letter_approval');
            $table->string('related_type')->nullable()->comment('Model yg memicu notifikasi');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID dari model yg memicu notifikasi');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            $table->timestamps();

            // Indexes
            $table->index('read_at');
            $table->index('category');
            $table->index(['related_type', 'related_id']);
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};