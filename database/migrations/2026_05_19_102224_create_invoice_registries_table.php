<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_registries', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('invoice_number')
                ->unique();

            $table->enum('source_table', [
                'invoice_reports',
                'awaiting_reports',
                'online_transactions',
            ]);

            $table->unsignedBigInteger('source_id');

            $table->boolean('is_suspicious')
                ->default(false);

            $table->string('suspicious_reason')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('invoice_number');
            $table->index('source_table');
            $table->index('source_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_registries');
    }
};