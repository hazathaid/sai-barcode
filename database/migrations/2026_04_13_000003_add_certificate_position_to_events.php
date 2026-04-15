<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'certificate_font_size')) {
                $table->unsignedInteger('certificate_font_size')->nullable()->default(36);
            }
            if (! Schema::hasColumn('events', 'certificate_text_x_pct')) {
                $table->unsignedTinyInteger('certificate_text_x_pct')->nullable()->default(50);
            }
            if (! Schema::hasColumn('events', 'certificate_text_y_pct')) {
                $table->unsignedTinyInteger('certificate_text_y_pct')->nullable()->default(60);
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'certificate_font_size')) {
                $table->dropColumn('certificate_font_size');
            }
            if (Schema::hasColumn('events', 'certificate_text_x_pct')) {
                $table->dropColumn('certificate_text_x_pct');
            }
            if (Schema::hasColumn('events', 'certificate_text_y_pct')) {
                $table->dropColumn('certificate_text_y_pct');
            }
        });
    }
};
