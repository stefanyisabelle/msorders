<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected OrderPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new OrderPolicy();
    }

    public function test_admin_can_update_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->updateStatus($admin, $order));
    }

    public function test_user_cannot_update_status_of_others(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $order = Order::factory()->create(['user_id' => $other->id]);
        $this->assertFalse($this->policy->updateStatus($user, $order));
    }

    public function test_user_can_update_own_order_status(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $order = Order::factory()->create(['user_id' => $user->id]);
        $this->assertFalse($this->policy->updateStatus($user, $order));
    }
}
