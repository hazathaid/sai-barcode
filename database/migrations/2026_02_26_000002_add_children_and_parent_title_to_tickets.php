<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'parent_title')) {
                $table->string('parent_title')->nullable()->after('parent_name');
            }

            if (! Schema::hasColumn('tickets', 'children')) {
                $table->json('children')->nullable()->after('parent_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'children')) {
                $table->dropColumn('children');
            }
            if (Schema::hasColumn('tickets', 'parent_title')) {
                $table->dropColumn('parent_title');
            }
        });
    }
};
