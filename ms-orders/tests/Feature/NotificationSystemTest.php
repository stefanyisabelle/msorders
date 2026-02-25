<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that notification is sent when order status changes.
     */
    public function test_notification_is_sent_when_order_status_changes(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $order = Order::factory()->create([
            'user_id' => $admin->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $admin->notify(new OrderStatusChangedNotification($order, Order::STATUS_PENDING));

        Notification::assertSentTo(
            $admin,
            OrderStatusChangedNotification::class,
            function ($notification) use ($order) {
                return $notification->order->id === $order->id;
            }
        );
    }

    /**
     * Test that notification contains correct data.
     */
    public function test_notification_contains_correct_data(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_CONFIRMED,
        ]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);

        $databaseData = $notification->toDatabase($user);

        $this->assertEquals($order->id, $databaseData['order_id']);
        $this->assertEquals(Order::STATUS_CONFIRMED, $databaseData['status']);
        $this->assertEquals(Order::STATUS_PENDING, $databaseData['previous_status']);
        $this->assertEquals($order->destination, $databaseData['destination']);

        $channels = $notification->via($user);
        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
    }

    /**
     * Test that notifications are stored in database.
     */
    public function test_notifications_are_stored_in_database(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);
        $user->notify($notification);

        Notification::assertSentTo($user, OrderStatusChangedNotification::class, function ($notification) use ($order) {
            return $notification->order->id === $order->id;
        });

        Notification::assertSentTo($user, OrderStatusChangedNotification::class, function ($notification) use ($user) {
            $channels = $notification->via($user);
            return in_array('database', $channels);
        });
    }

    /**
     * Test that user can retrieve their notifications.
     */
    public function test_user_can_retrieve_notifications(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);
        $user->notify($notification);
        Notification::assertSentTo($user, OrderStatusChangedNotification::class);
        
        Notification::assertSentTo($user, OrderStatusChangedNotification::class, function ($notification) use ($order) {
            $databaseData = $notification->toDatabase($order->user);
            return $databaseData['order_id'] === $order->id 
                && isset($databaseData['status'])
                && isset($databaseData['destination']);
        });
    }

    /**
     * Test that user can mark notification as read.
     */
    public function test_user_can_mark_notification_as_read(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);
        $user->notify($notification);

        Notification::assertSentTo($user, OrderStatusChangedNotification::class);
        
        Notification::assertSentTo($user, OrderStatusChangedNotification::class, function ($notification) use ($order) {
            $databaseData = $notification->toDatabase($order->user);
            return isset($databaseData['message']) && isset($databaseData['updated_at']);
        });
    }

    /**
     * Test notification queue configuration.
     */
    public function test_notification_is_queued(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);

        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $notification);

        $this->assertEquals(3, $notification->tries);
        $this->assertEquals(30, $notification->timeout);
        $this->assertEquals(60, $notification->backoff);
    }

    /**
     * Test notification email content.
     */
    public function test_notification_email_contains_order_details(): void
    {
        $user = User::factory()->create(['name' => 'João Silva']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_CONFIRMED,
            'destination' => 'São Paulo',
        ]);

        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('João Silva', $mailMessage->greeting);
        $this->assertStringContainsString("#{$order->id}", $mailMessage->introLines[0]);
        $this->assertStringContainsString('São Paulo', $mailMessage->introLines[2]);
    }
}
