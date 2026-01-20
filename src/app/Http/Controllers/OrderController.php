<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            ...$data,
            'status' => 'pending',
        ]);

        event(new OrderCreated($order));

        return response()->json($order->refresh(), 201);
    }

    public function show($id)
    {
        return Order::findOrFail($id);
    }
}
