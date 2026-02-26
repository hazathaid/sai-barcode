<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the unique constraint preventing multiple tickets with same email per event
        // Check INFORMATION_SCHEMA for index existence to avoid SQL errors
        $dbName = DB::getDatabaseName();
        $index = DB::selectOne("SELECT COUNT(1) AS cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'tickets' AND INDEX_NAME = 'tickets_event_id_email_unique'", [$dbName]);
        if ($index && ($index->cnt ?? 0) > 0) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropUnique('tickets_event_id_email_unique');
            });
        }

        // Add parent_name to store the guardian/parent name
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'parent_name')) {
                $table->string('parent_name')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'parent_name')) {
                $table->dropColumn('parent_name');
            }

            // Recreate the unique index if needed
            try {
                $table->unique(['event_id', 'email']);
            } catch (\Exception $e) {
                // ignore
            }
        });
    }
};
