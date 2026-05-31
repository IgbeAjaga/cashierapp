<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('online_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edited_by')->constrained('users');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('online_transaction_histories');
    }
};