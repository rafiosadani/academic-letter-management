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
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->id();

            // Letter info
            $table->string('letter_type', 50); // 'skak', 'skak_tunjangan', etc
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();

            // Form Data (JSON - dynamic per letter type)
            $table->json('data_input')->comment('Form fields sesuai jenis surat');

            // Status tracking
            $table->enum('status', [
                'in_progress',          // Sedang proses approval internal
                'external_processing',  // Khusus SKAK - di sistem pusat
                'approved',             // Sudah approved semua, siap publish
                'rejected',             // Ditolak
                'resubmitted',          // Mahasiswa submit ulang setelah ditolak
                'cancelled',            // Dibatalkan mahasiswa
                'completed',            // Final - sudah published & bisa diunduh
            ]);

            // External system status (khusus SKAK)
            $table->enum('external_system_status', [
                'waiting_upload',       // Belum upload draft Word
                'waiting_university',   // Sudah upload, proses di sistem UB
                'completed'             // PDF final dari UB sudah diterima
            ])->nullable()->comment('Status khusus untuk SKAK yang pakai sistem pusat');

            // Rejection info
            $table->text('rejected_reason')->nullable()->comment('Alasan reject terakhir (deprecated, pakai rejection_histories)');

            // Edit permission
            $table->boolean('is_editable')->default(false)->comment('Flag apakah mahasiswa boleh edit');

            $table->timestamps();
            $table->softDeletes();

            // Record Signature
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Indexes
            $table->index('letter_type');
            $table->index('student_id');
            $table->index('semester_id');
            $table->index('academic_year_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['student_id', 'status']);
            $table->index(['letter_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_requests');
    }
};
