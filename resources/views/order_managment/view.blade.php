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
                    <h5 class="card-title mb-0">Order ID :
                        {{ $order['order_code'] ?? 'N/A' }}</h5>
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

                <!-- Checkbox for sending orders to kitchen -->
                <label class="position-absolute" style="bottom: 10px; right: 10px; cursor: pointer;">
                    <input type="checkbox" style="display: none;" class="roundCheckbox"
                        data-order-id="{{ $order['order_id'] }}">
                    <span class="checkbox-circle"
                        style="display: inline-block;width: 40px;height: 40px;background-color: #007bff;border-radius: 50%;position: relative;border: none;transition: background-color 0.3s ease;">
                        <span
                            style="content: '\f067';font-family: 'Font Awesome 5 Free';font-weight: 900;color: white;font-size: 20px;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">&#xf067;</span>
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
// Select checkboxes and send orders button
const checkboxes = document.querySelectorAll('.roundCheckbox');
const sendOrdersButton = document.getElementById('sendOrdersButton');

// Automatically send orders where kitchen time equals current time
window.addEventListener('DOMContentLoaded', (event) => {
    const currentTime = new Date().toISOString(); // Current time in ISO format
    const orders = @json($orders); // Pass orders from the backend to JavaScript

    orders.forEach(order => {
        const kitchenTime = order.kitchen_time ? new Date(order.kitchen_time).toISOString() : null;

        if (kitchenTime && kitchenTime === currentTime) {
            sendToKitchen(order.order_id); // Send order to kitchen if times match
        }
    });
});

// Send selected orders to kitchen on button click
sendOrdersButton.addEventListener('click', function() {
    const selectedOrders = [];

    checkboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            selectedOrders.push(checkbox.getAttribute('data-order-id'));
        }
    });

    if (selectedOrders.length > 0) {
        sendToKitchen(selectedOrders);
    } else {
        alert('Please select at least one order.');
    }
});

// Function to send orders to the kitchen and update status to "In Progress"
function sendToKitchen(orderIds) {
    fetch('/send-orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_ids: Array.isArray(orderIds) ? orderIds : [orderIds]
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Selected orders have been sent to the kitchen.');
                // Optionally, update the UI status of orders here
            }
        })
        .catch(error => {
            console.error('Error sending orders:', error);
        });
}

// Toggle the checkbox background color
checkboxes.forEach((checkbox) => {
    const checkboxCircle = checkbox.nextElementSibling;

    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            checkboxCircle.style.backgroundColor = '#28a745'; // Green when checked
        } else {
            checkboxCircle.style.backgroundColor = '#007bff'; // Blue when unchecked
        }
    });
});
</script>
@endsection