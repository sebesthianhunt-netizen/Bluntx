<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\SnookerTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function createVenue(Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        $v = Venue::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
        return response()->json(['data' => $v]);
    }

    public function updateVenue($id, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'name' => 'sometimes|string|max:120',
            'address' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        $v = Venue::findOrFail($id);
        $v->fill($data);
        $v->save();
        return response()->json(['data' => $v]);
    }

    public function deleteVenue($id)
    {
        abort_unless(Gate::allows('admin'), 403);
        $v = Venue::findOrFail($id);
        $v->delete();
        return response()->json(['success' => true]);
    }

    public function createTable($venueId, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'table_number' => 'required|integer|min:1',
            'table_type' => 'required|string|max:40',
            'hourly_rate' => 'required|numeric|min:100',
        ]);
        $t = SnookerTable::create([
            'venue_id' => (int)$venueId,
            'table_number' => (int)$data['table_number'],
            'table_type' => $data['table_type'],
            'hourly_rate' => (int) ($data['hourly_rate'] * 100),
        ]);
        return response()->json(['data' => $t]);
    }

    public function updateTable($id, Request $request)
    {
        abort_unless(Gate::allows('admin'), 403);
        $data = $request->validate([
            'table_number' => 'sometimes|integer|min:1',
            'table_type' => 'sometimes|string|max:40',
            'hourly_rate' => 'sometimes|numeric|min:100',
        ]);
        $t = SnookerTable::findOrFail($id);
        if (isset($data['hourly_rate'])) $data['hourly_rate'] = (int)($data['hourly_rate'] * 100);
        $t->fill($data);
        $t->save();
        return response()->json(['data' => $t]);
    }

    public function deleteTable($id)
    {
        abort_unless(Gate::allows('admin'), 403);
        $t = SnookerTable::findOrFail($id);
        $t->delete();
        return response()->json(['success' => true]);
    }
}


