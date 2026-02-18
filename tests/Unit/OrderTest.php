<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_constants(): void
    {
        $this->assertContains(Order::STATUS_PENDING, Order::STATUSES);
        $this->assertContains(Order::STATUS_CONFIRMED, Order::STATUSES);
        $this->assertContains(Order::STATUS_CANCELLED, Order::STATUSES);
    }

    public function test_fillable_and_casts(): void
    {
        $order = Order::factory()->make();
        $this->assertArrayHasKey('customer_name', $order->getAttributes());
        $this->assertArrayHasKey('destination', $order->getAttributes());
        $this->assertArrayHasKey('departure_date', $order->getAttributes());
        $this->assertArrayHasKey('return_date', $order->getAttributes());
        $this->assertArrayHasKey('status', $order->getAttributes());
        $this->assertArrayHasKey('user_id', $order->getAttributes());
    }

    public function test_belongs_to_user(): void
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf(User::class, $order->user);
    }
}
