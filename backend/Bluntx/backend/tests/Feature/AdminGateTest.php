<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_endpoint_requires_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        $token = $user->createToken('t')->accessToken;
        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/admin/feature-toggle')
            ->assertStatus(403);

        $admin = User::factory()->create(['role' => 'SuperAdmin']);
        $admToken = $admin->createToken('t')->accessToken;
        $this->withHeaders(['Authorization' => 'Bearer ' . $admToken])
            ->postJson('/api/v1/admin/feature-toggle')
            ->assertOk();
    }
}


