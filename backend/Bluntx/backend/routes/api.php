<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\ChallengeController;
use App\Http\Controllers\Api\V1\TournamentController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Api\V1\EscrowController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ModerationController;
use App\Http\Controllers\Api\V1\DeviceController;

Route::prefix('v1')->group(function () {
    // Auth (rate limited)
    Route::middleware('throttle:otp')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/otp/verify', [AuthController::class, 'verify']);
    });

    // Public Webhooks (no auth)
    Route::post('/webhooks/{provider}', [WebhookController::class, 'handle']);

    // Public browsing endpoints
    Route::get('/venues', [BookingController::class, 'venues']);
    Route::get('/venues/{venueId}/tables', [BookingController::class, 'tables']);
    Route::get('/tournaments', [TournamentController::class, 'index']);
    Route::get('/tournaments/{id}/bracket', [TournamentController::class, 'bracket']);
    Route::get('/feed', fn () => response()->json(['data' => [
        ['id' => 1, 'user_id' => 1, 'type' => 'text', 'caption' => 'First post!']
    ]]));

    // Protected routes (require auth:api)
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        // Profile
        Route::get('/user/profile', function (Request $r) {
            $u = $r->user('api') ?? $r->user();
            return response()->json([
                'id' => $u->id,
                'phone' => $u->phone,
                'nickname' => $u->name,
                'email' => $u->email,
                'avatar_url' => null,
                'bio' => null,
                'role' => $u->role ?? 'User',
            ]);
        });
        Route::patch('/user/profile', function (Request $r) {
            $u = $r->user('api') ?? $r->user();
            $data = $r->validate([
                'nickname' => 'sometimes|string|max:80',
                'email' => 'sometimes|email|max:255',
                'avatar_url' => 'sometimes|url|max:2048',
                'bio' => 'sometimes|string|max:280',
            ]);
            if (isset($data['nickname'])) $u->name = $data['nickname'];
            if (isset($data['email'])) $u->email = $data['email'];
            $u->save();
            return response()->json([
                'id' => $u->id,
                'phone' => $u->phone,
                'nickname' => $u->name,
                'email' => $u->email,
                'avatar_url' => null,
                'bio' => $data['bio'] ?? null,
                'role' => $u->role ?? 'User',
            ]);
        });

        // Wallet (controller)
        Route::get('/wallet', [WalletController::class, 'show']);
        Route::get('/wallet/history', [WalletController::class, 'history']);
        Route::post('/wallet/fund', [WalletController::class, 'fund']);
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
        Route::post('/wallet/withdraw/{reference}/confirm', [WalletController::class, 'withdrawConfirm']);
        Route::post('/wallet/withdraw/{reference}/cancel', [WalletController::class, 'withdrawCancel']);
        Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
        Route::get('/wallet/tx/{reference}', [WalletController::class, 'showTx']);

        // Booking (controller)
        Route::post('/booking', [BookingController::class, 'create']);
        Route::get('/booking/{id}', [BookingController::class, 'show']);
        Route::post('/booking/waitlist', [BookingController::class, 'waitlist']);
        Route::post('/booking/{id}/checkin', [BookingController::class, 'checkin'])->middleware('can:attendant');
        Route::post('/booking/{id}/checkout', [BookingController::class, 'checkout'])->middleware('can:attendant');
        Route::get('/booking/qr/{id}', fn ($id) => response()->json(['qr_code' => 'data:image/png;base64,iVBORw0KGgoAAA...']));

        // Challenges (controller)
        Route::get('/challenge/history', [ChallengeController::class, 'history']);
        Route::post('/challenge', [ChallengeController::class, 'create']);
        Route::post('/challenge/{id}/accept', [ChallengeController::class, 'accept']);
        Route::post('/challenge/{id}/decline', [ChallengeController::class, 'decline']);
        Route::post('/challenge/{id}/result', [ChallengeController::class, 'result']);
        Route::post('/challenge/{id}/dispute', [ChallengeController::class, 'dispute']);

        // U2U Escrow (on-site)
        Route::post('/escrow/initiate', [EscrowController::class, 'initiate']);
        Route::post('/escrow/{id}/confirm', [EscrowController::class, 'confirm']);
        Route::post('/escrow/{id}/cancel', [EscrowController::class, 'cancel']);

        // Tournaments (controller)
        Route::post('/tournaments/{id}/register', [TournamentController::class, 'register']);
        Route::post('/tournaments/{id}/seed', [TournamentController::class, 'seed']);
        Route::post('/tournaments/{id}/result', [TournamentController::class, 'resultMatch']);

        // Devices / notifications
        Route::post('/devices/register', [DeviceController::class, 'register']);

        // Feed (create)
        Route::post('/feed', fn (Request $r) => response()->json(['success' => true, 'caption' => $r->caption]));

        // Moderation
        Route::post('/flags', [ModerationController::class, 'flag']);
        Route::patch('/flags/{id}', [ModerationController::class, 'review']);

        // Admin endpoints (RBAC via gates)
        Route::post('/admin/feature-toggle', function (Request $r) {
            abort_unless(Gate::allows('admin'), 403);
            return response()->json(['success' => true]);
        });
        Route::get('/admin/audit', function () {
            abort_unless(Gate::allows('admin'), 403);
            $logs = \Illuminate\Support\Facades\DB::table('audit_logs')->orderByDesc('id')->limit(100)->get();
            return response()->json(['data' => $logs]);
        });
        Route::post('/admin/venues', [AdminController::class, 'createVenue']);
        Route::patch('/admin/venues/{id}', [AdminController::class, 'updateVenue']);
        Route::delete('/admin/venues/{id}', [AdminController::class, 'deleteVenue']);
        Route::post('/admin/venues/{venueId}/tables', [AdminController::class, 'createTable']);
        Route::patch('/admin/tables/{id}', [AdminController::class, 'updateTable']);
        Route::delete('/admin/tables/{id}', [AdminController::class, 'deleteTable']);
    });

});
