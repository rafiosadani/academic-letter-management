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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Key-value structure
            $table->string('key', 100)->unique()->comment('Unique setting key (e.g., header_faculty');
            $table->text('value')->nullable()->comment('Setting value');

            // Metadata
            $table->enum('type', ['string', 'text', 'image'])->default('string')->comment('Value type for validation');
            $table->string('group', 50)->nullable()->comment('Group category (general, header, footer)');
            $table->string('label')->nullable()->comment('Display label for UI');
            $table->text('description')->nullable()->comment('Helper text/description');
            $table->integer('order')->default(0)->comment('Display order in form');

            $table->timestamps();

            // Indexes
            $table->index('key');
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
