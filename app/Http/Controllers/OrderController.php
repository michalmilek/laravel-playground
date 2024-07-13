<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Rules\EnumValue;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Get list of orders",
     *     description="Returns list of orders",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->get();
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     description="Creates a new order and returns it",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="items", type="array", 
     *                 @OA\Items(type="object", 
     *                     @OA\Property(property="product_id", type="integer"), 
     *                     @OA\Property(property="quantity", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = new Order();
        $order->user_id = Auth::id();
        $order->total_amount = 0; // This will be calculated
        $order->status = Status::PENDING;
        $order->save();

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $product->price * $item['quantity'];
            $orderItem->save();

            $totalAmount += $orderItem->price;
        }

        $order->total_amount = $totalAmount;
        $order->save();

        return response()->json(['message' => 'Order created successfully', 'order' => $order->load('items.product')]);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get a single order",
     *     description="Returns a single order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Update order status",
     *     description="Updates the status of an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", description="New status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
           'status' => ['required', 'string', new EnumValue(Status::class)]
        ]); 

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order]);
    }
}
