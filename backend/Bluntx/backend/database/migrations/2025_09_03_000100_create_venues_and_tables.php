<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('venues', function (Blueprint $table) {
			$table->id();
			$table->string('name', 120);
			$table->string('address', 255)->nullable();
			$table->decimal('latitude', 10, 7)->nullable();
			$table->decimal('longitude', 10, 7)->nullable();
			$table->string('phone', 20)->nullable();
			$table->boolean('is_active')->default(true);
			$table->timestamps();
		});

		Schema::create('snooker_tables', function (Blueprint $table) {
			$table->id();
			$table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
			$table->unsignedSmallInteger('table_number');
			$table->string('table_type', 50)->nullable();
			$table->bigInteger('hourly_rate')->default(0);
			$table->timestamps();
			$table->unique(['venue_id','table_number']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('snooker_tables');
		Schema::dropIfExists('venues');
	}
};
