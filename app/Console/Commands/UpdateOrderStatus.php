<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Orders;
use Carbon\Carbon;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update the status of orders when kitchen time matches current time.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get current timestamp
        $currentTime = Carbon::now()->toDateTimeString();

        // Find orders where kitchen_time matches the current time
        $ordersToUpdate = Orders::where('kitchen_time', '<=', $currentTime)
            ->where('status', '!=', 1) // Only update if not already "In Progress"
            ->get();

        if ($ordersToUpdate->isEmpty()) {
            $this->info('No orders to update at this time.');
            return 0;
        }

        // Update the status of the orders
        foreach ($ordersToUpdate as $order) {
            $order->status = 1; // Mark as "In Progress"
            $order->save();
        }

        $this->info(count($ordersToUpdate) . ' orders were updated successfully.');
        return 0;
    }
}