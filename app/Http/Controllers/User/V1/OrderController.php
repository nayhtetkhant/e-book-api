<?php

namespace App\Http\Controllers\User\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\V1\OrderStoreRequest;
use App\Http\Requests\User\V1\OrderUpdateRequest;
use App\Http\Resources\User\V1\OrderResource;
use App\Http\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        public OrderService $orderService,
    )
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            OrderResource::collection($this->orderService->getAllOrders())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        $result = $this->orderService->createOrder($request->validated('products'));

        return response()->json([
            'order' => OrderResource::make($result['order']),
            'checkoutUrl' => $result['checkoutUrl'],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json(
            OrderResource::make($this->orderService->getOrder($order))
        );
    }   

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        return response()->json(
            OrderResource::make($this->orderService->updateOrder($order, $request->validated())),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
