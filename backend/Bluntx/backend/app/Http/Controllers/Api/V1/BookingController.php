<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\SnookerTable;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\BookingCreateRequest;

class BookingController extends Controller
{
    public function venues()
    {
        $venues = Venue::where('is_active', true)
            ->get(['id','name']);
        return response()->json(['data' => $venues]);
    }

    public function tables($venueId, Request $request)
    {
        $date = $request->query('date');
        $tables = SnookerTable::where('venue_id', $venueId)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'table_number' => $t->table_number,
                    'table_type' => $t->table_type,
                    'hourly_rate' => (int) ($t->hourly_rate / 100),
                    'is_available' => true,
                    'available_slots' => [
                        ['start_time' => '14:00', 'end_time' => '15:00', 'price' => 2000],
                        ['start_time' => '15:00', 'end_time' => '16:00', 'price' => 2000],
                    ],
                ];
            });
        return response()->json(['data' => $tables]);
    }

    public function create(BookingCreateRequest $request)
    {
        $data = $request->validated();
        $table = SnookerTable::findOrFail($data['table_id']);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $booking = Booking::create([
            'user_id' => $user->id,
            'venue_id' => $table->venue_id,
            'snooker_table_id' => $table->id,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'total_amount' => 0,
            'status' => 'confirmed',
            'notes' => $data['notes'] ?? null,
        ]);
        return response()->json(['id' => $booking->id, 'status' => $booking->status]);
    }

    public function show($id)
    {
        $b = Booking::findOrFail($id);
        return response()->json([
            'id' => $b->id,
            'venue_id' => $b->venue_id,
            'snooker_table_id' => $b->snooker_table_id,
            'start_time' => $b->start_time,
            'end_time' => $b->end_time,
            'status' => $b->status,
            'notes' => $b->notes,
        ]);
    }

    public function waitlist(Request $request)
    {
        $data = $request->validate([
            'venue_id' => 'required|integer',
            'snooker_table_id' => 'nullable|integer',
            'desired_start_time' => 'required|date',
            'desired_end_time' => 'required|date|after:desired_start_time',
        ]);
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $id = DB::table('booking_waitlists')->insertGetId([
            'user_id' => $user->id,
            'venue_id' => (int) $data['venue_id'],
            'snooker_table_id' => $data['snooker_table_id'] ?? null,
            'desired_start_time' => $data['desired_start_time'],
            'desired_end_time' => $data['desired_end_time'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['id' => $id, 'status' => 'pending']);
    }

    public function checkin($id)
    {
        $b = Booking::findOrFail($id);
        $attendant = request()->user('api') ?? request()->user();
        abort_unless($attendant && in_array($attendant->role ?? 'User', ['SuperAdmin','Admin','Attendant']), 403);
        DB::table('booking_checkins')->insert([
            'booking_id' => $b->id,
            'attendant_id' => $attendant->id,
            'checked_in_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $b->status = 'in_progress';
        $b->save();
        return response()->json(['success' => true]);
    }

    public function checkout($id)
    {
        $b = Booking::findOrFail($id);
        $attendant = request()->user('api') ?? request()->user();
        abort_unless($attendant && in_array($attendant->role ?? 'User', ['SuperAdmin','Admin','Attendant']), 403);
        DB::table('booking_checkins')->where('booking_id', $b->id)->update(['checked_out_at' => now(), 'updated_at' => now()]);
        $b->status = 'completed';
        $b->save();
        return response()->json(['success' => true]);
    }
}
