<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('webhook_events')) {
            return;
        }
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('reference')->nullable();
            $table->string('event')->nullable();
            $table->string('signature')->nullable();
            $table->string('status')->default('received');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['provider','reference','event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};


