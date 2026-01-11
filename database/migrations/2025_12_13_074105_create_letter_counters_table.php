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
        Schema::create('letter_counters', function (Blueprint $table) {
            $table->id();

            // Counter tracking
            $table->string('letter_type', 50)->comment('Jenis surat');
            $table->integer('year')->comment('Tahun counter');
            $table->integer('last_sequence')->default(0)->comment('Nomor urut terakhir yg digunakan');

            // Hanya updated_at - tidak ada user tracking
            $table->timestamp('updated_at')->nullable();

            // Kolom Unique
            $table->unique(['letter_type', 'year'], 'unique_letter_type_year');

            // Indexes
            $table->index('letter_type');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_counters');
    }
};
