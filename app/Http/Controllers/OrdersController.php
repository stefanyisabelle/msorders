<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdersController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = auth()->user();
        $orders = $this->orderService->getOrders($request->all(), $user);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = auth()->user();
        $order = $this->orderService->createOrder($request->validated(), $user);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): OrderResource
    {
        $user = auth()->user();
        $order = $this->orderService->getOrder($id, $user);

        return new OrderResource($order);
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, int $id): OrderResource
    {
        $user = auth()->user();
        $order = $this->orderService->updateOrder($id, $request->validated(), $user);

        return new OrderResource($order);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): OrderResource
    {
        $user = auth()->user();
        $order = $this->orderService->updateOrderStatus($id, $request->validated(), $user);

        return new OrderResource($order);
    }
}
