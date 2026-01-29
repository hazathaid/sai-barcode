<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure there's an index on `event_id` so the foreign key constraint still has an index
        Schema::table('tickets', function (Blueprint $table) {
            // add index on event_id if not exists
            $table->index('event_id');
        });

        // Now drop the unique constraint on (event_id, email)
        Schema::table('tickets', function (Blueprint $table) {
            // dropUnique accepts the index name or column array
            if (Schema::hasColumn('tickets', 'event_id') && Schema::hasColumn('tickets', 'email')) {
                // use explicit index name to be safe
                $table->dropUnique('tickets_event_id_email_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unique(['event_id', 'email']);
        });
    }
};
