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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('letter_request_id')->constrained('letter_requests')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('category', ['supporting', 'generated', 'external']);
            $table->enum('type', ['draft', 'final'])->nullable();

            $table->string('file_name');
            $table->string('file_path', 500);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);

            $table->string('hash', 64)->nullable();

            $table->timestamps();

            // Indexes
            $table->index('letter_request_id');
            $table->index('category');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
