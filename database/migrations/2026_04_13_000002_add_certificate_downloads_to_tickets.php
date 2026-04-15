<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'certificate_downloads')) {
                $table->unsignedInteger('certificate_downloads')->default(0)->after('qr_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'certificate_downloads')) {
                $table->dropColumn('certificate_downloads');
            }
        });
    }
};
