<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());
    }

    public function test_is_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }

    public function test_jwt_identifier(): void
    {
        $user = User::factory()->create();
        $this->assertEquals($user->id, $user->getJWTIdentifier());
        $this->assertIsArray($user->getJWTCustomClaims());
    }

    public function test_orders_relationship(): void
    {
        $user = User::factory()->create();
        $this->assertTrue(method_exists($user, 'orders'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->orders());
    }
}
