<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class OrderStatusChangedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_via_channels(): void
    {
        $order = Order::factory()->make();
        $notification = new OrderStatusChangedNotification($order);
        $channels = $notification->via(new User());
        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
    }

    public function test_to_mail_returns_mail_message(): void
    {
        $user = User::factory()->make(['name' => 'Maria']);
        $order = Order::factory()->make(['id' => 123, 'destination' => 'Recife']);
        $notification = new OrderStatusChangedNotification($order);
        $mail = $notification->toMail($user);
        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Maria', $mail->greeting);
        $this->assertStringContainsString('#123', $mail->introLines[0]);
        $this->assertStringContainsString('Recife', implode(' ', $mail->introLines));
    }

    public function test_to_database_and_to_array(): void
    {
        $order = Order::factory()->make(['id' => 99, 'status' => Order::STATUS_CONFIRMED]);
        $notification = new OrderStatusChangedNotification($order, Order::STATUS_PENDING);
        $user = User::factory()->make();
        $data = $notification->toDatabase($user);
        $array = $notification->toArray($user);
        unset($data['updated_at'], $array['updated_at']);
        $this->assertEquals(99, $data['order_id']);
        $this->assertEquals(Order::STATUS_CONFIRMED, $data['status']);
        $this->assertEquals(Order::STATUS_PENDING, $data['previous_status']);
        $this->assertEquals($data, $array);
    }
}
