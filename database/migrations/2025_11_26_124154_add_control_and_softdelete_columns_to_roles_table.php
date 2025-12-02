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
            // Kolom code
            if (!Schema::hasColumn('roles', 'code')) {
                $table->string('code', 20)->unique()->after('id');
            }

            // Kolom Kontrol UI (is_editable, is_deletable)
            if (!Schema::hasColumn('roles', 'is_editable')) {
                $table->boolean('is_editable')->default(true)->after('guard_name');
            }

            if (!Schema::hasColumn('roles', 'is_deletable')) {
                $table->boolean('is_deletable')->default(true)->after('is_editable');
            }

            // Kolom Signature (created_by, updated_by, deleted_by)
            if (!Schema::hasColumn('roles', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
//                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('roles', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
//                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('roles', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
//                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }

            // Soft Delete
            if (!Schema::hasColumn('roles', 'deleted_at')) {
                $table->softDeletes()->after('deleted_by');
            }

            if (Schema::hasColumn('roles', 'created_by')) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasColumn('roles', 'updated_by')) {
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasColumn('roles', 'deleted_by')) {
                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }

            // Indexes
            $table->index('code');
            $table->index('name');
            $table->index('is_editable');
            $table->index('is_deletable');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'is_editable',
                'is_deletable',
                'created_by',
                'updated_by',
                'deleted_by',
                'deleted_at',
            ]);
        });
    }
};
