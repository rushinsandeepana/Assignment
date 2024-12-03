@extends('adminlte::page')

@section('title', 'Order Details')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Order Details</h1>
    <button id="sendOrdersButton" class="btn btn-primary">Send Orders</button>
</div>
@endsection

@section('content')
<div class="container pt-2"
    style="max-height: 600px; overflow-y: auto; border: 1px solid #ccc; scrollbar-width: none; -ms-overflow-style: none;">
    @if ($orders && count($orders) > 0)
    <div class="row">
        @foreach ($orders as $order)
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Order ID: {{ $order['order_code'] ?? 'N/A' }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Concessions:</strong></p>
                    <ul>
                        @foreach ($order['concessions'] as $concession)
                        <li>{{ $concession['concession_name'] ?? 'Unnamed' }} - {{ $concession['quantity'] ?? 0 }}</li>
                        @endforeach
                    </ul>
                    <p><strong>Kitchen Time:</strong>
                        {{ $order['kitchen_time'] ? \Carbon\Carbon::parse($order['kitchen_time'])->format('Y-m-d h:i A') : 'N/A' }}
                    </p>
                    <p><strong>Total Cost:</strong> Rs.{{ number_format($order['total_cost'] ?? 0, 2) }}</p>
                    <p><strong>Status:</strong>
                        <span
                            class="badge badge-{{ $order['status'] == 2 ? 'success' : ($order['status'] == 1 ? 'info' : 'warning') }}">
                            @if($order['status'] == 0)
                            Pending
                            @elseif($order['status'] == 1)
                            In Progress
                            @elseif($order['status'] == 2)
                            Completed
                            @else
                            Unknown
                            @endif
                        </span>
                    </p>
                </div>
                <label class="position-absolute" style="bottom: 10px; right: 10px; cursor: pointer;">
                    <input type="checkbox" style="display: none;" class="roundCheckbox"
                        data-order-id="{{ $order['order_id'] }}">
                    <span class="checkbox-circle"
                        style="display: inline-block;width: 40px;height: 40px;background-color: #f2efea;border-radius: 50%;position: relative;border: none;transition: background-color 0.3s ease;">
                        <span
                            style="content: '\f067';font-family: 'Font Awesome 5 Free';font-weight: 900;color: #0d6efd;font-size: 20px;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">&#xf067;</span>
                    </span>
                </label>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-center text-muted">No orders available.</p>
    @endif
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.roundCheckbox');
    const orders = @json($orders); // Laravel blade variable
    console.log("Is orders an array?", Array.isArray(orders));
    console.log("Orders type:", typeof orders);

    // Function to automatically send orders to the kitchen
    function sendToKitchen(orderIds) {
        fetch('/send-orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    order_ids: Array.isArray(orderIds) ? orderIds : [orderIds]
                })
            })
            .then(response => response.json())
            .then(data => {
                // No alert needed, orders sent automatically
                checkboxes.forEach((checkbox) => {
                    const orderId = checkbox.getAttribute('data-order-id');
                    if (orderIds.includes(orderId)) {
                        checkbox.disabled = true;
                        const card = checkbox.closest('.card');
                        card.style.opacity = '0.6';
                        card.style.pointerEvents = 'none';
                    }
                });
            })
            .catch(error => {
                console.error('Error sending orders:', error);
            });
    }

    function autoSendOrders() {
        const currentTime = new Date();
        const currentTimeString = currentTime.toISOString().slice(0, 16);
        console.log("Current Time:", currentTimeString);

        Object.keys(orders).forEach(orderKey => {
            const order = orders[orderKey];

            let kitchenTime = null;
            if (order.kitchen_time) {
                if (order.kitchen_time.length <= 8) {
                    const currentDate = currentTime.toISOString().slice(0, 10);
                    order.kitchen_time = `${currentDate}T${order.kitchen_time}`;
                }

                const parsedKitchenTime = new Date(order.kitchen_time);
                if (!isNaN(parsedKitchenTime)) {
                    kitchenTime = parsedKitchenTime.toISOString().slice(0, 16);
                } else {
                    console.warn(
                        `Invalid kitchen time for Order ID ${order.order_id}: ${order.kitchen_time}`
                    );
                }
            }

            console.log("Kitchen Time for Order ID " + order.order_id + ":", kitchenTime);

            if (kitchenTime && kitchenTime === currentTimeString) {
                order.status = 1; // Update the order status to 'sent_to_kitchen'

                // Send the order to the kitchen automatically
                sendToKitchen(order.order_id);

                // Disable checkbox and update card UI
                const checkbox = document.querySelector(
                    `.roundCheckbox[data-order-id="${order.order_id}"]`);
                if (checkbox) {
                    checkbox.disabled = true;
                    const card = checkbox.closest('.card');
                    card.style.opacity = '0.6';
                    card.style.pointerEvents = 'none';
                }
            }
        });
    }

    // Check orders every minute
    setInterval(autoSendOrders, 60000);
});
</script>
@endsection