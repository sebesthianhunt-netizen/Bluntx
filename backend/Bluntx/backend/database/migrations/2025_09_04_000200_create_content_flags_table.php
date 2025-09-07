<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('content_type');
            $table->unsignedBigInteger('content_id');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, removed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_flags');
    }
};


