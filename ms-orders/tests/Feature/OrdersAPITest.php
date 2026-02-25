<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrdersAPITest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected string $userToken;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => User::ROLE_USER]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->userToken = JWTAuth::fromUser($this->user);
        $this->adminToken = JWTAuth::fromUser($this->admin);
    }

    public function test_user_can_create_order(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/orders', [
                'customer_name' => 'João Silva',
                'destination' => 'São Paulo',
                'departure_date' => now()->addDays(5)->format('Y-m-d'),
                'return_date' => now()->addDays(10)->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'customer_name',
                'destination',
                'departure_date',
                'return_date',
                'status',
                'user_id',
            ])
            ->assertJson([
                'customer_name' => 'João Silva',
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'João Silva',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_list_only_own_orders(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create(); // Other users' orders

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_admin_can_list_all_orders(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_user_can_view_own_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $order->id]);
    }

    public function test_user_cannot_view_other_user_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_order(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->patchJson("/api/orders/{$order->id}", [
                'destination' => 'Rio de Janeiro',
            ]);

        $response->assertStatus(200)
            ->assertJson(['destination' => 'Rio de Janeiro']);
    }

    public function test_user_cannot_update_status(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->patchJson("/api/orders/{$order->id}/status", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_status(): void
    {
        $order = Order::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/orders/{$order->id}/status", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'confirmed']);
    }

    public function test_cannot_cancel_confirmed_order(): void
    {
        $order = Order::factory()->confirmed()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/orders/{$order->id}/status", [
                'status' => 'cancelled',
            ]);

        $response->assertStatus(500);
    }

    public function test_can_filter_orders_by_status(): void
    {
        Order::factory()->count(2)->pending()->create(['user_id' => $this->admin->id]);
        Order::factory()->count(3)->confirmed()->create(['user_id' => $this->admin->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/orders?status=confirmed');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_filter_orders_by_destination(): void
    {
        Order::factory()->create(['user_id' => $this->admin->id, 'destination' => 'São Paulo']);
        Order::factory()->create(['user_id' => $this->admin->id, 'destination' => 'Rio']);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/orders?destination=São Paulo');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_unauthenticated_user_cannot_access_orders(): void
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    public function test_validation_errors_on_create_order(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/orders', [
                'customer_name' => '',
                'destination' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'destination', 'departure_date', 'return_date']);
    }
}
