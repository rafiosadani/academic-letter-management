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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'code')) {
                $table->string('code', 20)->unique()->after('id')->comment('Kode unik user (e.g., USR/YYMM/001)');
            }

            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(1)->after('password')->comment('1=aktif, 0=nonaktif');
            }

            // Kolom Signature (created_by, updated_by, deleted_by)
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }

            // Soft delete
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('deleted_by');
            }

            $table->index('code');
            $table->index('email');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys dulu
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);

            // Drop columns
            $table->dropColumn([
                'code',
                'status',
                'created_by',
                'updated_by',
                'deleted_by',
                'deleted_at',
            ]);

            // Restore columns yang dihapus
            $table->string('name')->after('id');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
