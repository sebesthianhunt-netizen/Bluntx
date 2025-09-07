<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use App\Models\SnookerTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_details_and_checkin_checkout(): void
    {
        $user = User::factory()->create(['phone' => '+2348000000000']);
        $token = $user->createToken('t')->accessToken;
        $venue = Venue::factory()->create(['is_active' => true]);
        $table = SnookerTable::factory()->create(['venue_id' => $venue->id]);

        $resp = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/booking', [
                'table_id' => $table->id,
                'start_time' => now()->addHour()->toISOString(),
                'end_time' => now()->addHours(2)->toISOString(),
            ])->assertOk()->json();

        $bookingId = $resp['id'];

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/booking/' . $bookingId)
            ->assertOk();

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/booking/' . $bookingId . '/checkin')
            ->assertOk();

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/booking/' . $bookingId . '/checkout')
            ->assertOk();
    }
}


