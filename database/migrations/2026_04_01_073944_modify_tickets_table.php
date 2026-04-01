<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Schema;

class ModifyTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('tickets', function ($table) {
            $table->string('email')->nullable()->change();
            $table->unique(['email'], 'unique_email')->whereNull('email');
            $table->index('phone');
        });
    }

    public function down()
    {
        Schema::table('tickets', function ($table) {
            $table->dropUnique('unique_email');
            $table->dropIndex(['phone']);
            $table->string('email')->nullable(false)->change();
        });
    }
}
