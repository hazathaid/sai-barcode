<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'registrant_type')) {
                $table->string('registrant_type', 20)->default('parent')->after('parent_title')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'registrant_type')) {
                $table->dropIndex(['registrant_type']);
                $table->dropColumn('registrant_type');
            }
        });
    }
};
