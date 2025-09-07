<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        abort_unless($user, 401, 'Unauthenticated');
        $data = $request->validate([
            'device_id' => 'required|string|max:180',
            'push_token' => 'required|string|max:1024',
            'platform' => 'required|string|in:ios,android,web',
        ]);
        $ud = UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $data['device_id']],
            ['push_token' => $data['push_token'], 'platform' => $data['platform']]
        );
        return response()->json(['data' => $ud]);
    }
}


