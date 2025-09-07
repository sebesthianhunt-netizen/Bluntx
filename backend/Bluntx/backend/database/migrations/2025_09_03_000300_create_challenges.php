<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenger_id');
            $table->unsignedBigInteger('opponent_id');
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->bigInteger('stake_amount')->default(0); // kobo
            $table->bigInteger('insurance_amount')->default(0); // kobo
            $table->bigInteger('total_escrow')->default(0); // kobo
            $table->enum('status', ['pending','accepted','declined','expired','in_progress','completed','disputed'])->default('pending');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->index(['challenger_id','opponent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
