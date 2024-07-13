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
    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->get();
        return response()->json($orders);
    }

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

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }

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
