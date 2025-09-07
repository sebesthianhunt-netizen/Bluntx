<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('escrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiator_id');
            $table->unsignedBigInteger('receiver_id');
            $table->bigInteger('amount');
            $table->string('currency', 8)->default('NGN');
            $table->string('asset', 16)->default('cash');
            $table->string('status', 24)->default('pending');
            $table->string('reference')->unique();
            $table->string('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrows');
    }
};


