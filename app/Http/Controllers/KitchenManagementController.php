<?php

namespace App\Http\Controllers;

use App\Models\LevelOne;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KitchenManagementController extends Controller
{


    public function orderDetails()
    {
        $orders = Orders::all();
        $currentTime = Carbon::now();

        foreach ($orders as $order) {
            if ($order['kitchen_time'] && Carbon::parse($order['kitchen_time'])->isSameMinute($currentTime)) {
                $order->status = 'sent_to_kitchen';
                $order->save();
            }
        }

        return view('order_managment.view', compact('orders'));
    }

    public function sendToKitchen(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
    
        if (empty($orderIds)) {
            return response()->json(['success' => false, 'message' => 'No orders provided.']);
        }
    
        $updatedCount = Orders::whereIn('id', $orderIds)
            ->where('status', '!=', 1)
            ->update(['status' => 1]);
    
        if ($updatedCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "$updatedCount orders sent to the kitchen.",
                'updated_orders' => $orderIds
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'No valid orders to update. They might already be in progress or not found.'
        ]);
    }

    public function view()
    {
        $orders = LevelOne::leftJoin('orders', 'level_ones.order_id', '=', 'orders.id')
            ->leftJoin('concessions', 'level_ones.concession_id', '=', 'concessions.id')
            ->select(
                'orders.id as order_id',
                'orders.order_code',
                'orders.amount as total_cost',
                'concessions.id as concession_id',
                'concessions.name as concession_name',
                'level_ones.quantity',
                'orders.status as status'
            )->whereIn('orders.status', [1, 2]) 
            ->get();
    
        $groupedOrders = $orders->groupBy('order_id')->map(function ($items) {
            $order = $items->first();
            return [
                'order_id' => $order->order_id,
                'order_code' => $order->order_code,
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
        })->toArray();
    
        // dd($groupedOrders);
    
        return view('kitchen_management.view', ['orders' => $groupedOrders]);
    }

    public function markAsCompleted($orderId)
    {
        $order = Orders::find($orderId);

        if ($order && $order->status == 1) {
            $order->status = 2;
            $order->save();
            return redirect()->back()->with('success', 'Order marked as completed.');
        }

        return redirect()->back()->with('error', 'Order cannot be completed at this stage.');
    }
    
    public function deleteOrder($orderId)
{
    $order = Orders::find($orderId);
    if ($order) {
        $order->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Order not found'], 404);
}

    
}