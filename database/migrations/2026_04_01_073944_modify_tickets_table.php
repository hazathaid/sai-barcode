<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Schema;
use Illuminate\Database\Schema\Blueprint;

class ModifyTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('email')->nullable()->change();  // Make email nullable
            $table->index('phone'); // Add non-unique index on phone
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();  // Revert to non-nullable
            $table->dropIndex(['phone']); // Drop the non-unique index on phone
        });
    }
}