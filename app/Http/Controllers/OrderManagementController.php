<?php

namespace App\Http\Controllers;

use App\Models\Concessions;
use App\Models\Orders;
use App\Models\LevelOne;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderManagementController extends Controller
{
    public function add()
    {
        return view('order_managment.add');
    }

    public function index()
    {
        $items = Concessions::all();
        
        return view('order_managment.add', compact('items'));
    }

    public function create(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'amount' => 'required|numeric',
                'kitchen_time' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:concessions,id',
                'items.*.item_name' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
            ]);
    
            // Order creation logic
            $order = Orders::create([
                'order_code' => 'ORD' . Str::random(10), 
                'amount' => $validated['amount'],
                'kitchen_date' => now()->format('Y-m-d'),
                'kitchen_time' => $validated['kitchen_time'],
                'status' => 0,
            ]);
    
            // Create related items
            foreach ($validated['items'] as $item) {
                LevelOne::create([
                    'order_id' => $order->id,
                    'concession_id' => $item['item_id'], 
                    'quantity' => $item['quantity'],
                ]);
            }
    
            // Return success response
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'redirect_url' => route('order.view'),
                'message' => 'Order created successfully.',
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function viewAll()
    {
        $orders = LevelOne::leftJoin('orders', 'level_ones.order_id', '=', 'orders.id')
            ->leftJoin('concessions', 'level_ones.concession_id', '=', 'concessions.id')
            ->select(
                'orders.id as order_id',
                'orders.order_code',
                'orders.kitchen_time',
                'orders.amount as total_cost',
                'concessions.id as concession_id',
                'concessions.name as concession_name',
                'level_ones.quantity',
                'orders.status as status'
            )
            ->get();
    
        // Group concessions by order
        $groupedOrders = $orders->groupBy('order_id')->map(function ($items) {
            $order = $items->first(); // Get common order details
            return [
                'order_id' => $order->order_id,
                'order_code' => $order->order_code,
                'kitchen_time' => $order->kitchen_time,
                'total_cost' => $order->total_cost,
                'status' => $order->status,
                'concessions' => $items->map(function ($item) {
                    return [
                        'concession_id' => $item->concession_id,
                        'concession_name' => $item->concession_name,
                        'quantity' => $item->quantity,
                    ];
                })->toArray(),
            ];
        });
    
        // Debugging
        // dd($groupedOrders);
    
        return view('order_managment.view', ['orders' => $groupedOrders]);
    }
    
    
    
}