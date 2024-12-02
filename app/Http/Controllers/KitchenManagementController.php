<?php

namespace App\Http\Controllers;

use App\Models\LevelOne;
use App\Models\Orders;
use Carbon\Carbon;

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

        return view('admin.orders', compact('orders'));
    }

    public function sendToKitchen(Request $request)
    {
        $orderIds = $request->input('order_ids');
        
        foreach ($orderIds as $orderId) {
            $order = Orders::find($orderId);
            
            if ($order && $order->status == 0) {
                $order->status = 1;
                $order->save(); 
            }
        }
        
        return response()->json(['success' => true]);
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
    
        // Group concessions by order
        $groupedOrders = $orders->groupBy('order_id')->map(function ($items) {
            $order = $items->first(); // Get common order details
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
        });
    
        // dd($groupedOrders);
    
        return view('kitchen_management.view', ['orders' => $groupedOrders]);
    }

    public function markAsCompleted($orderId)
{
    $order = Orders::find($orderId);

    if ($order && $order->status == 1) {  // Only allow marking as completed if the order is "In Progress"
        $order->status = 2; // Mark as "Completed"
        $order->save();
        return redirect()->back()->with('success', 'Order marked as completed.');
    }

    return redirect()->back()->with('error', 'Order cannot be completed at this stage.');
}
    
}