<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    public function test_create_order_success(): void
    {
        $user = User::factory()->create();
        $data = [
            'customer_name' => 'João',
            'destination' => 'São Paulo',
            'departure_date' => '2026-03-01',
            'return_date' => '2026-03-10',
        ];
        $order = $this->service->createOrder($data, $user);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($user->id, $order->user_id);
    }

    public function test_create_order_invalid_dates(): void
    {
        $user = User::factory()->create();
        $data = [
            'customer_name' => 'João',
            'destination' => 'São Paulo',
            'departure_date' => '2026-03-10',
            'return_date' => '2026-03-01',
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->service->createOrder($data, $user);
    }

    public function test_update_order_status_success(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
        ]);
        Gate::shouldReceive('allows')
            ->with('updateStatus', \Mockery::on(fn($o) => $o->id === $order->id))
            ->andReturn(true);
        $updated = $this->service->updateOrderStatus($order->id, [
            'status' => Order::STATUS_CONFIRMED
        ], $user);
        $this->assertEquals(Order::STATUS_CONFIRMED, $updated->status);
    }

    public function test_update_order_status_unauthorized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
        ]);
        Gate::shouldReceive('allows')
            ->with('updateStatus', \Mockery::on(fn($o) => $o->id === $order->id))
            ->andReturn(false);
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $this->service->updateOrderStatus($order->id, [
            'status' => Order::STATUS_CONFIRMED
        ], $user);
    }

    public function test_update_order_status_cannot_cancel_confirmed(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_CONFIRMED,
        ]);
        Gate::shouldReceive('allows')->with('updateStatus', $order)->andReturn(true);
        $this->expectException(\RuntimeException::class);
        $this->service->updateOrderStatus($order->id, [
            'status' => Order::STATUS_CANCELLED
        ], $user);
    }
}
