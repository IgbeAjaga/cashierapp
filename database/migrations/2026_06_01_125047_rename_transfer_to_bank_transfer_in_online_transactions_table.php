<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('online_transactions', function (Blueprint $table) {
           // Rename the column
            $table->renameColumn('transfer', 'bank_transfer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            // Rollback
            $table->renameColumn('bank_transfer', 'transfer');
        });
    }
};
