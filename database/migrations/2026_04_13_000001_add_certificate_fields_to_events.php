<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'certificate_image')) {
                $table->string('certificate_image')->nullable();
            }
            if (! Schema::hasColumn('events', 'certificate_font')) {
                $table->string('certificate_font')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'certificate_image')) {
                $table->dropColumn('certificate_image');
            }
            if (Schema::hasColumn('events', 'certificate_font')) {
                $table->dropColumn('certificate_font');
            }
        });
    }
};
