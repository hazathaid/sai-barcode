<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete()->unique();
            $table->unsignedBigInteger('checked_in_by')->nullable();
            $table->timestamp('checked_in_at');
            $table->timestamps();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('checked_in_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by']);
        });
        Schema::dropIfExists('attendances');
    }
};
