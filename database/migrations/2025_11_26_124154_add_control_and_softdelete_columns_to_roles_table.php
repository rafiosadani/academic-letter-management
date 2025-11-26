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
        Schema::table('roles', function (Blueprint $table) {
            // Tambahan UI
            $table->boolean('is_editable')->default(true)->after('guard_name');
            $table->boolean('is_deletable')->default(true)->after('is_editable');

            // Soft delete
            if (!Schema::hasColumn('roles', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'is_editable',
                'is_deletable',
                'deleted_at'
            ]);
        });
    }
};
