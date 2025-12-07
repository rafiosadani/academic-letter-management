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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->string('category', 50)->comment('Kategori notifikasi (misalnya: academic_year, semester, letter_approval');

            // Channel preferences
            $table->boolean('channel_database')->default(1)->comment('Notifikasi di dalam aplikasi (In App), default: AKTIF');
            $table->boolean('channel_email')->default(0)->comment('Notifikasi melalui Email, default: NONAKTIF');

            // Email preferences
            $table->boolean('email_immediately')->default(0)->comment('Mengirim email segera setelah peristiwa (default: NONAKTIF)');
            $table->boolean('email_daily_digest')->default(0)->comment('Mengirim email dalam bentuk ringkasan harian (default: NONAKTIF)');

            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Unique constraint
            $table->unique(['user_id', 'category'], 'unique_user_category');

            // Indexes
            $table->index('user_id');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};