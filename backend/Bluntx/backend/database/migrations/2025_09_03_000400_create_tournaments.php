<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->enum('tournament_type', ['single_elimination','double_elimination','round_robin','swiss'])->default('single_elimination');
            $table->bigInteger('entry_fee')->default(0);
            $table->bigInteger('prize_pool')->default(0);
            $table->integer('max_participants')->default(64);
            $table->enum('status', ['upcoming','registration_open','registration_closed','in_progress','completed'])->default('registration_open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
