<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::query();
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->query('venue_id'));
        }
        $items = $query->orderByDesc('id')->get();
        return response()->json(['data' => $items]);
    }

    public function register($id)
    {
        $user = request()->user('api') ?? request()->user();
        abort_unless($user, 401, 'Unauthenticated');
        DB::table('tournament_participants')->insert([
            'tournament_id' => (int)$id,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function bracket($id)
    {
        // Simple example: list participants in pairs
        $participants = DB::table('tournament_participants')->where('tournament_id', (int)$id)->pluck('user_id')->toArray();
        $round = [];
        for ($i = 0; $i < count($participants); $i += 2) {
            $round[] = [
                'match_id' => $i/2 + 1,
                'player_a_id' => $participants[$i] ?? null,
                'player_b_id' => $participants[$i+1] ?? null,
                'status' => 'scheduled',
            ];
        }
        return response()->json([
            'tournament_id' => (int) $id,
            'rounds' => [[ 'round' => 1, 'matches' => $round ]]
        ]);
    }

    public function seed($id, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'user_ids' => 'required|array|min:2',
            'user_ids.*' => 'integer',
        ]);
        DB::table('tournament_participants')->where('tournament_id', (int)$id)->delete();
        $now = now();
        DB::table('tournament_participants')->insert(array_map(fn($uid) => [
            'tournament_id' => (int)$id,
            'user_id' => (int)$uid,
            'created_at' => $now,
            'updated_at' => $now,
        ], $data['user_ids']));
        return response()->json(['success' => true]);
    }

    public function resultMatch($id, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'match_id' => 'required|integer',
            'winner_id' => 'required|integer',
        ]);
        // Persist match result to a table in production; here echo back
        return response()->json(['success' => true, 'tournament_id' => (int)$id, 'result' => $data]);
    }
}
