<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $timeout = 30;

    public $backoff = 60;

    public Order $order;
    protected string $previousStatus;

    /**
     * Create a new notification instance.
     *
    * @param Order $order
     * @param string|null $previousStatus Status anterior do pedido
     */
    public function __construct(Order $order, ?string $previousStatus = null)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus ?? $order->status;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            Order::STATUS_PENDING => 'aguardando aprovação',
            Order::STATUS_CONFIRMED => 'confirmado',
            Order::STATUS_CANCELLED => 'cancelado',
        ];

        $statusText = $statusMessages[$this->order->status] ?? $this->order->status;
        $subject = "Pedido #{$this->order->id} - Status Atualizado";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Seu pedido #{$this->order->id} teve o status atualizado.")
            ->line("**Novo status:** " . ucfirst($statusText))
            ->line("**Destino:** {$this->order->destination}")
            ->line("**Data de partida:** {$this->order->departure_date}")
            ->line("**Data de retorno:** {$this->order->return_date}")
            ->action('Ver Detalhes do Pedido', url("/api/orders/{$this->order->id}"))
            ->line('Obrigado por utilizar nosso serviço!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'previous_status' => $this->previousStatus,
            'destination' => $this->order->destination,
            'departure_date' => $this->order->departure_date,
            'return_date' => $this->order->return_date,
            'message' => "Seu pedido #{$this->order->id} foi atualizado para {$this->order->status}.",
            'updated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications',
            'database' => 'notifications',
        ];
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'notification',
            'order:' . $this->order->id,
            'user:' . $this->order->user_id,
            'status:' . $this->order->status,
        ];
    }
}
