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
                'message' => 'Order created successfully.',
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    
    
}