<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('account_type'); // cash, points, escrow
            $table->timestamps();
            $table->unique(['owner_type','owner_id','account_type']);
        });

        Schema::create('ledger_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // fund, withdraw, transfer, escrow_lock, escrow_release
            $table->string('status')->default('success');
            $table->bigInteger('total_amount')->default(0);
            $table->string('currency', 3)->default('NGN');
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->index(['type','status']);
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_transaction_id')->constrained('ledger_transactions')->cascadeOnDelete();
            $table->foreignId('ledger_account_id')->constrained('ledger_accounts')->cascadeOnDelete();
            $table->enum('direction', ['debit','credit']);
            $table->bigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->string('reference')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['ledger_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('ledger_transactions');
        Schema::dropIfExists('ledger_accounts');
    }
};
