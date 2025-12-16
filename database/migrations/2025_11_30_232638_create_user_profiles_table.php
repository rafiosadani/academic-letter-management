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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Personal Info
            $table->string('full_name');
            $table->string('place_of_birth')->nullable()->comment('Tempat lahir (for SKAK letters)');
            $table->date('date_of_birth')->nullable()->comment('Tanggal lahir (for SKAK letters)');
            $table->string('student_or_employee_id', 50)->unique()->nullable()->comment('NIM/NIP');
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable()->comment('Path foto di storage');
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete()->comment('ID program studi');
            $table->text('address')->nullable();

            // Parent Info (SKAK Tunjangan)
            $table->string('parent_name')->nullable()->comment('Nama orang tua');
            $table->string('parent_nip', 30)->nullable()->comment('NIP orang tua');
            $table->string('parent_rank')->nullable()->comment('Pangkat/Golongan orang tua');
            $table->string('parent_institution')->nullable()->comment('Nama instansi orang tua');
            $table->text('parent_institution_address')->nullable()->comment('Alamat instansi orang tua');

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('student_or_employee_id');
            $table->index('study_program_id');
            $table->index('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
