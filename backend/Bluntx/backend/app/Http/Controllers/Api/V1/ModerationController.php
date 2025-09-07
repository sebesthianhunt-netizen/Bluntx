<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ModerationController extends Controller
{
    public function flag(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $data = $request->validate([
            'content_type' => 'required|string',
            'content_id' => 'required|integer',
            'reason' => 'nullable|string|max:200',
        ]);
        $id = DB::table('content_flags')->insertGetId([
            'user_id' => $user->id,
            'content_type' => $data['content_type'],
            'content_id' => (int)$data['content_id'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['id' => $id, 'status' => 'pending']);
    }

    public function review($id, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'status' => 'required|string|in:pending,reviewed,removed',
        ]);
        DB::table('content_flags')->where('id', (int)$id)->update([
            'status' => $data['status'],
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }
}


