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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();

            // Step info (copied from approval_flows)
            $table->integer('step')->comment('Nomor urut step');
            $table->string('step_label', 100)->comment('Label step');
            $table->json('required_positions')->comment('Array of positions (snapshot from approval_flows)');

            // Approver assignment (snapshot at letter request creation)
            $table->foreignId('assigned_approver_id')->nullable()->constrained('users')->nullOnDelete()->comment('Pejabat yang ditugaskan saat surat dibuat (snapshot)');

            // Actual approver (who clicked approve/reject button)
            $table->foreignId('actual_approver_id')->nullable()->constrained('users')->nullOnDelete()->comment('User yang actually melakukan approve/reject (could be different if delegated)');

            // Status approval
            $table->enum('status', [
                'pending',     // Menunggu action
                'approved',    // Disetujui
                'rejected',    // Ditolak
                'skipped',     // Dilewati (rare case)
                'published'    // Khusus step publish - surat diterbitkan
            ])->default('pending');

            // Notes dari approver
            $table->text('note')->nullable()->comment('Catatan dari approver (visible ke mahasiswa)');

            // Content editing tracking
            $table->boolean('content_edited')->default(false)->comment('Flag apakah approver edit data');
            $table->json('edited_fields')->nullable()->comment('Detail field yang diedit');

            // Timestamps
            $table->timestamp('approved_at')->nullable()->comment('Waktu approve/reject');

            // Current step indicator
            $table->boolean('is_active')->default(false)->comment('Step yang sedang berjalan');

            $table->timestamps();
            $table->softDeletes();

            // Record Signature
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Indexes
            $table->index('letter_request_id');
            $table->index('step');
            $table->index('assigned_approver_id');
            $table->index('actual_approver_id');
            $table->index('status');
            $table->index('is_active');
            $table->index(['letter_request_id', 'is_active']);
            $table->index(['assigned_approver_id', 'status']);
            $table->index(['actual_approver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
