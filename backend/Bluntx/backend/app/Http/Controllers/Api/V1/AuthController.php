<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Models\UserDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'device_id' => 'nullable|string',
            'device_type' => 'nullable|string',
            'platform' => 'nullable|string',
            'push_token' => 'nullable|string',
        ]);

        $phone = $this->normalizePhone($data['phone']);

        // Generate OTP (stub: 123456 in non-prod)
        $otpCode = app()->environment('production') ? (string) random_int(100000, 999999) : '123456';
        Otp::create([
            'phone' => $phone,
            'code' => $otpCode,
            'purpose' => 'login',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // TODO: Integrate SMS provider
        Log::info('OTP generated', ['phone' => $phone, 'code' => $otpCode]);

        return response()->json(['success' => true, 'message' => 'OTP sent']);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
            'device_id' => 'nullable|string',
            'device_type' => 'nullable|string',
            'platform' => 'nullable|string',
            'push_token' => 'nullable|string',
        ]);

        $phone = $this->normalizePhone($data['phone']);

        $otp = Otp::where('phone', $phone)
            ->where('purpose', 'login')
            ->orderByDesc('id')
            ->first();

        if (!$otp || $otp->expires_at->isPast() || $otp->attempts >= 5) {
            return response()->json(['success' => false, 'message' => 'OTP expired or invalid'], 422);
        }
        if (!hash_equals($otp->code, $data['code'])) {
            $otp->increment('attempts');
            return response()->json(['success' => false, 'message' => 'Incorrect code'], 422);
        }

        // Upsert user
        $user = User::firstOrCreate([
            'phone' => $phone,
        ], [
            'name' => 'Player ' . Str::substr($phone, -4),
            'email' => null,
            'password' => Hash::make(Str::random(12)),
        ]);

        // Track device
        UserDevice::create([
            'user_id' => $user->id,
            'device_id' => $data['device_id'] ?? null,
            'device_type' => $data['device_type'] ?? null,
            'platform' => $data['platform'] ?? null,
            'push_token' => $data['push_token'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Issue Passport token
        $tokenResult = $user->createToken('blvkdot');
        $accessToken = $tokenResult->accessToken;
        $expiresAt = $tokenResult->token->expires_at?->timestamp ?? (now()->addHour()->timestamp);

        // Invalidate OTP after success
        $otp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'nickname' => $user->name,
                    'elo_rating' => 1250,
                    'tier' => 'silver',
                    'xp_points' => 0,
                    'level' => 1,
                    'is_verified' => true,
                ],
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $expiresAt - time(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        if ($user && $user->token()) {
            $user->token()->revoke();
        }
        return response()->json(['success' => true]);
    }

    private function normalizePhone(string $phone): string
    {
        $p = preg_replace('/\D+/', '', $phone);
        if (Str::startsWith($p, '0')) {
            $p = '234' . substr($p, 1);
        }
        if (!Str::startsWith($p, '234')) {
            $p = '234' . $p;
        }
        return '+' . $p;
    }
}
