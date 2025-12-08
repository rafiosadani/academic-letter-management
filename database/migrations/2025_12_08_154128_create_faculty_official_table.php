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
        Schema::create('faculty_officials', function (Blueprint $table) {
            $table->id();

            // Core Relations
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User yang menjabat');

            $table->string('position', 50)
                ->comment('Jabatan: Dekan, Wakil Dekan Bidang Akademik, etc');

            $table->foreignId('study_program_id')
                ->nullable()
                ->constrained('study_programs')
                ->onDelete('set null')
                ->comment('Program studi terkait (wajib jika Kaprodi)');

            // Period
            $table->date('start_date')
                ->comment('Tanggal mulai menjabat');

            $table->date('end_date')
                ->nullable()
                ->comment('Tanggal selesai menjabat (NULL = masih aktif)');

            // Additional Info
            $table->text('notes')
                ->nullable()
                ->comment('Catatan tambahan');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['user_id', 'position'], 'idx_user_position');
            $table->index(['start_date', 'end_date'], 'idx_date_range');
            $table->index('study_program_id', 'idx_study_program');
            $table->index('position', 'idx_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_official');
    }
};
