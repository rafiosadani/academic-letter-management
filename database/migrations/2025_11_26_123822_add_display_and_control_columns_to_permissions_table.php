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
        Schema::table('permissions', function (Blueprint $table) {
            // Tambahan kolom UI
            $table->string('display_group_name')->nullable()->after('guard_name');
            $table->string('display_name')->nullable()->after('display_group_name');

            // Pengaturan (UI boleh diedit / tidak)
            $table->boolean('is_editable')->default(true)->after('display_name');
            $table->boolean('is_deletable')->default(true)->after('is_editable');

            // Soft delete
            if (!Schema::hasColumn('permissions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn([
                'display_group_name',
                'display_name',
                'is_editable',
                'is_deletable',
                'deleted_at'
            ]);
        });
    }
};
