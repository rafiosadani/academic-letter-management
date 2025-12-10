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
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();

            // Jenis surat (menggunakan enum LetterType)
            $table->string('letter_type', 50); // 'skak', 'skak_tunjangan', 'penelitian', 'dispensasi_kuliah', 'dispensasi_mahasiswa'

            // Step approval
            $table->integer('step')->comment('Urutan step (1, 2, 3, ...)');
            $table->string('step_label', 100)->comment('Label yang ditampilkan ke user');

            // Jabatan yang handle step ini (multiple positions allowed)
            $table->json('required_positions')->comment('Array of positions yang bisa handle step ini');

            // Permissions untuk approver
            $table->boolean('can_edit_content')->default(false)->comment('Boleh edit data_input minor');
            $table->boolean('is_editable')->default(false)->comment('Mahasiswa boleh edit jika ditolak di step ini');

            // Action saat reject
            $table->enum('on_reject', ['to_student', 'to_previous_step', 'terminate'])
                ->default('to_student')
                ->comment('Kemana surat dikembalikan saat reject');

            // Flag final step
            $table->boolean('is_final')->default(false)->comment('Step terakhir (generate nomor surat)');

            $table->timestamps();

            // Record Signature
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Indexes
            $table->unique(['letter_type', 'step'], 'unique_letter_type_step');
            $table->index('letter_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_flows');
    }
};
