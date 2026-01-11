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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();

            $table->string('code', 20)->unique()->comment('Format: TA-2024/2025');
            $table->string('year_label', 20)->unique()->comment('Format: 2024/2025');
            $table->date('start_date')->comment('Tanggal mulai tahun akademik');
            $table->date('end_date')->comment('Tanggal akhir tahun akademik');
            $table->boolean('is_active')->default(0)->comment('Hanya 1 yang bisa aktif');

            $table->timestamps();

            // Signature columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('code');
            $table->index('year_label');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
