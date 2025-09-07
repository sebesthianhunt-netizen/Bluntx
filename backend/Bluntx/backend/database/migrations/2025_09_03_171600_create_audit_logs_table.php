<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guard')->nullable();
            $table->string('method', 10);
            $table->string('route');
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->smallInteger('status');
            $table->string('request_hash');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['user_id','route']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};


