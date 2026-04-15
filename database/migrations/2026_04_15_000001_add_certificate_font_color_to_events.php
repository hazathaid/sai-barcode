<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'certificate_font_color')) {
                $table->string('certificate_font_color', 7)->nullable()->default('#000000');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'certificate_font_color')) {
                $table->dropColumn('certificate_font_color');
            }
        });
    }
};
