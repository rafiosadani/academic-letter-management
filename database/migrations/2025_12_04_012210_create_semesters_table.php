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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique()->comment('Format: TA-2024/2025-GJL');
            $table->unsignedBigInteger('academic_year_id');
            $table->enum('semester_type', ['Ganjil', 'Genap'])->comment('Jenis semester');
            $table->date('start_date')->nullable()->comment('Tanggal mulai semester');
            $table->date('end_date')->nullable()->comment('Tanggal akhir semester');
            $table->boolean('is_active')->default(0)->comment('Hanya 1 yang bisa aktif di seluruh sistem');

            $table->timestamps();

            // Foreign key
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();

            // Indexes & Unique
            $table->unique(['academic_year_id', 'semester_type'], 'unique_semester_year');
            $table->index('code');
            $table->index('academic_year_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
