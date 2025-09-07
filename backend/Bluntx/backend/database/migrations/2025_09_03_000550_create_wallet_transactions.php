<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // fund, withdraw, transfer, escrow_lock, escrow_release
            $table->string('status')->default('pending'); // pending, success, failed, reversed, flagged
            $table->bigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->string('provider')->nullable();
            $table->string('reference')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['user_id','type','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
