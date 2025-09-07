<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('webhook_events') && !Schema::hasColumn('webhook_events', 'retry_count')) {
            Schema::table('webhook_events', function (Blueprint $table) {
                $table->unsignedInteger('retry_count')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('webhook_events') && Schema::hasColumn('webhook_events', 'retry_count')) {
            Schema::table('webhook_events', function (Blueprint $table) {
                $table->dropColumn('retry_count');
            });
        }
    }
};


