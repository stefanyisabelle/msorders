<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Returns orders based on filters and user.
     *
     * @param array<string, mixed> $filters
     * @param User $user
    * @return Collection<int, Order>
     */
    public function getOrders(array $filters, User $user): Collection
    {
        $query = Order::query();

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply destination filter
        if (!empty($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        // Apply date range filter for departure and return dates
        if (!empty($filters['departure_date']) || !empty($filters['return_date'])) {
            if (!empty($filters['departure_date']) && !empty($filters['return_date'])) {
                $query->whereBetween('departure_date', [$filters['departure_date'], $filters['return_date']]);
            } else {
                if (!empty($filters['departure_date'])) {
                    $query->whereDate('departure_date', '=', $filters['departure_date']);
                }
                if (!empty($filters['return_date'])) {
                    $query->whereDate('return_date', '=', $filters['return_date']);
                }
            }
        }

        // Apply created_at filter
        if (!empty($filters['start_created_at']) && !empty($filters['end_created_at'])) {
            $query->whereBetween('created_at', [$filters['start_created_at'], $filters['end_created_at']]);
        } elseif (!empty($filters['created_at'])) {
            $query->whereDate('created_at', '=', $filters['created_at']);
        }

        // Regular users only see their own orders
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Creates a new order.
     *
     * @param array<string, mixed> $data
     * @param User $user
    * @return Order
     * @throws \InvalidArgumentException
     */
    public function createOrder(array $data, User $user): Order
    {
        $this->validateDates($data['departure_date'], $data['return_date']);

        $data['user_id'] = $user->id;
        $data['status'] = Order::STATUS_PENDING;

        $order = Order::create($data);

        Log::info('Order created', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);

        return $order;
    }

    /**
     * Updates an existing order.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @param User $user
    * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    public function updateOrder(int $id, array $data, User $user): Order
    {
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Validate dates if they are being updated
        $departureDate = $data['departure_date'] ?? $order->departure_date;
        $returnDate = $data['return_date'] ?? $order->return_date;
        $this->validateDates($departureDate, $returnDate);

        $order->fill($data);
        $order->save();

        Log::info('Order updated', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);

        return $order;
    }

    /**
     * Updates the order status.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @param User $user
    * @return Order
     * @throws AuthorizationException
     * @throws \RuntimeException
     */
    public function updateOrderStatus(int $id, array $data, User $user): Order
    {
        $order = Order::findOrFail($id);

        if (!Gate::allows('updateStatus', $order)) {
            throw new AuthorizationException('User is not authorized to update the status of this order.');
        }

        // Business rule: confirmed orders cannot be cancelled
        if ($order->isConfirmed() && $data['status'] === Order::STATUS_CANCELLED) {
            throw new \RuntimeException('A confirmed order cannot be cancelled.');
        }

        $oldStatus = $order->status;
        $order->status = $data['status'];
        $order->save();

        Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status,
            'updated_by' => $user->id,
        ]);


        $notification = new OrderStatusChangedNotification($order, $oldStatus);
        $order->user->notify($notification);
        Log::info('Notification sent', [
            'order_id' => $order->id,
            'user_id' => $order->user->id,
            'can_mail' => in_array('mail', $notification->via($order->user)),
            'can_database' => in_array('database', $notification->via($order->user)),
        ]);

        return $order;
    }

    /**
     * Returns a specific order.
     *
     * @param int $id
     * @param User $user
    * @return Order
     * @throws AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getOrder(int $id, User $user): Order
    {
        $order = Order::findOrFail($id);

        if (!Gate::allows('view', $order)) {
            throw new AuthorizationException('User is not authorized to view this order.');
        }

        return $order;
    }

    /**
     * Validates that departure date is not after return date.
     *
     * @param string $departureDate
     * @param string $returnDate
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateDates(string $departureDate, string $returnDate): void
    {
        if (Carbon::parse($departureDate)->gt(Carbon::parse($returnDate))) {
            throw new \InvalidArgumentException('The departure date cannot be after the return date.');
        }
    }
}
