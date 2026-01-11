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
        Schema::create('letter_number_configs', function (Blueprint $table) {
            $table->id();

            // Letter type configuration
            $table->string('letter_type', 50)->unique()->comment('Jenis surat (penelitian, dispensasi_kuliah, etc)');

            // Number format components
            $table->string('prefix', 50)->comment('Prefix kode unit (contoh: UN10.F1601)');
            $table->string('code', 10)->comment('Kode jenis surat (contoh: LL, DK, DM');
            $table->string('padding')->default(3)->comment('Jumlah digit sequence (3 = 001, 4 = 0001');

            $table->timestamps();

            // Record Signature
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Indexes
            $table->index('letter_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_number_configs');
    }
};
