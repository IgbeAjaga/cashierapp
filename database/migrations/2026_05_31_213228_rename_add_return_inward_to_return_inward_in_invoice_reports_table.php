<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_reports', function (Blueprint $table) {
            // Rename the column
            $table->renameColumn('add_return_inward', 'return_inward');
        });
    }

    public function down()
    {
        Schema::table('invoice_reports', function (Blueprint $table) {
            // Rollback
            $table->renameColumn('return_inward', 'add_return_inward');
        });
    }
};