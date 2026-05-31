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
        Schema::create('online_transactions', function (Blueprint $table) {

            $table->id();

            $table->string('invoice_number')->nullable();

            $table->string('customer')->nullable();

            $table->text('payment_note')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);

            $table->decimal('cash', 15, 2)->default(0);

            $table->decimal('cheque', 15, 2)->default(0);

            $table->text('cheque_details')->nullable();

            $table->decimal('pos', 15, 2)->default(0);

            $table->decimal('transfer', 15, 2)->default(0);

            $table->decimal('return_inward', 15, 2)->default(0);

            $table->decimal('less_credit', 15, 2)->default(0);

            $table->decimal('balance_to_use', 15, 2)->default(0);

            $table->decimal('difference', 15, 2)->default(0);

            $table->boolean('is_balanced')->default(true);

            $table->date('date');

            $table->enum('shift', [
                'morning',
                'afternoon',
                'night'
            ]);

            $table->foreignId('recorded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('edited_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('last_edited_at')
                ->nullable();

            $table->softDeletes();

            $table->timestamps();

            // INDEXES
            $table->index('invoice_number');
            $table->index('customer');
            $table->index('date');
            $table->index('shift');
            $table->index('is_balanced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_transactions');
    }
};