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
        Schema::create('rejection_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();
            $table->foreignId('approval_id')->nullable()->constrained('approvals')->nullOnDelete();

            // Rejection info
            $table->integer('step')->comment('Step ke berapa ditolak');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();

            // Rejection details
            $table->enum('rejection_type', [
                'data_invalid',         // Data tidak valid
                'format_error',         // Format salah
                'requirement_not_met',  // Syarat tidak terpenuhi
                'policy_violation',     // Melanggar kebijakan
                'incomplete_document',  // Dokumen tidak lengkap
                'other'                 // Lainnya
            ])->comment('Kategori penolakan untuk analytics');

            $table->text('reason')->comment('Alasan detail penolakan');

            $table->timestamp('rejected_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('letter_request_id');
            $table->index('approval_id');
            $table->index('rejected_by');
            $table->index('rejection_type');
            $table->index('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rejection_histories');
    }
};
