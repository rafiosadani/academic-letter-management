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
        Schema::create('letter_cancellations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();

            // Cancellation info
            $table->text('reason')->nullable()->comment('Alasan pembatalan (opsional)');
            $table->timestamp('cancelled_at')->useCurrent();

            $table->timestamps();

            // Indexes
            $table->index('letter_request_id');
            $table->index('cancelled_by');
            $table->index('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_cancellations');
    }
};
