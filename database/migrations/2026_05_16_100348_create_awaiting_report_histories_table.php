<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('awaiting_report_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('awaiting_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edited_by')->constrained('users');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awaiting_report_histories');
    }
};