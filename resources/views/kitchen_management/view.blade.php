@extends('adminlte::page')

@section('title', 'Kitchen Order Details')

@section('content_header')
<h1>Kitchen Order Details</h1>
@endsection

@section('content')
<div class="container pt-2">
    @if ($orders && count($orders) > 0)
    <table class="table table-bordered shadow-lg" style="border-radius: 8px;">
        <thead class="thead-dark">
            <tr>
                <th>Order ID</th>
                <th>Concession Name</th>
                <th>Quantity</th>
                <th>Total Cost</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody
            style="max-height: 580px; overflow-y: auto; border: 1px solid #ccc; scrollbar-width: none; -ms-overflow-style: none;">
            @foreach ($orders as $order)
            <tr style="border: 1px solid #ddd;">
                <td>{{ $order['order_code'] ?? 'N/A' }}</td>
                <td>
                    @foreach ($order['concessions'] as $concession)
                    <div>{{ $concession['concession_name'] ?? 'Unnamed' }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach ($order['concessions'] as $concession)
                    <div>{{ $concession['quantity'] ?? 0 }}</div>
                    @endforeach
                </td>
                <td>Rs.{{ number_format($order['total_cost'] ?? 0, 2) }}</td>
                <td>
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
                </td>
                <td>
                    <!-- Show 'Mark as Completed' button if the order is in progress -->
                    @if($order['status'] == 1)
                    <form action="{{ route('order.complete', $order['order_id']) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Complete Order</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-center text-muted">No orders available.</p>
    @endif
</div>
@endsection