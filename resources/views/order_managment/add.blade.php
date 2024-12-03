@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Add Order') }}
</h2>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-8"
            style="max-height: 600px; overflow-y: auto; border: 1px solid #ccc; border: 1px solid #ccc; -ms-overflow-style: none; scrollbar-width: none;">
            <div class="container my-3">
                @foreach ($items as $item)
                <div class="row bg-white mb-3 mt-3 border border-dark" style="height: 120px;">
                    <div class="col">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('assets/default-image.jpg') }}"
                            class="img-fluid" alt="Item Image" style="width: 300px; height: 118px; object-fit: cover;">
                    </div>
                    <div class="col d-flex align-items-center justify-content-center text-4xl font-weight-bold">
                        {{ $item->name }}
                    </div>
                    <div class="col d-flex align-items-center justify-content-center">
                        Rs. {{ $item->price }}
                    </div>
                    <div class="col d-flex align-items-center">
                        <button class="btn btn-primary p-2 decrease-btn">-</button>
                        <input type="number" class="form-control mx-2 quantity-input" value="1" min="1"
                            style="width: 60px;">
                        <button class="btn btn-primary p-2 increase-btn">+</button>
                    </div>
                    <div class="col d-flex flex-column align-items-center justify-content-center">
                        <button class="btn btn-success mb-2 add-item-btn" data-item-id="{{ $item->id }}"
                            data-item-name="{{ $item->name }}" data-item-price="{{ $item->price }}">Add</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-sm-4">
            <form action="{{ route('order.store') }}" method="POST" id="order-form">
                @csrf
                <div class="bg-secondary p-4 rounded shadow-sm bg-opacity-25"
                    style="max-height: 600px; overflow-y: auto; border: 1px solid #ccc; max-height: 600px; overflow-y: auto; border: 1px solid #ccc; border: 1px solid #ccc; -ms-overflow-style: none; scrollbar-width: none;">
                    <h3 class="text-center mb-4">Order Summary</h3>
                    <div class="mb-4">
                        <h5>Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="summaryTable">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="summaryItems"></tbody>
                            </table>
                        </div>
                        <a href="#" class="text-primary" id="showMoreLink" style="display:none;">Show More</a>
                    </div>
                    <div class="mb-4">
                        <h5>Kitchen Time</h5>
                        <input type="datetime-local" name="kitchen_time" class="form-control" id="kitchenTimeInput"
                            required>
                    </div>
                    <div class="mb-4">
                        <h5>Total Amount</h5>
                        <p><strong id="totalAmount">Rs. 0</strong></p>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitOrderBtn">ADD
                            ORDER</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const kitchenTimeInput = document.getElementById('kitchenTimeInput');

    const getCurrentDateTime = () => {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const date = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${date}T${hours}:${minutes}`;
    };

    kitchenTimeInput.setAttribute('min', getCurrentDateTime());

    setInterval(() => {
        kitchenTimeInput.setAttribute('min', getCurrentDateTime());
    }, 60000);

    const addItemButtons = document.querySelectorAll('.add-item-btn');
    const summaryTableBody = document.getElementById('summaryItems');
    const showMoreLink = document.getElementById('showMoreLink');
    const totalAmountElement = document.getElementById('totalAmount');
    const maxVisibleRows = 5;
    const orderForm = document.getElementById('order-form');
    let totalAmount = 0;

    const updateTotalAmount = () => {
        totalAmount = Array.from(summaryTableBody.querySelectorAll('tr'))
            .reduce((sum, row) => {
                const price = parseFloat(row.getAttribute('data-item-price'));
                const quantity = parseInt(row.querySelector('td:nth-child(2)').textContent);
                return sum + price * quantity;
            }, 0);
        totalAmountElement.textContent = `Rs. ${totalAmount.toFixed(2)}`;
    };

    const updateRowVisibility = () => {
        const rows = summaryTableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.style.display = index < maxVisibleRows ? '' : 'none';
        });
        showMoreLink.style.display = rows.length > maxVisibleRows ? '' : 'none';
    };

    addItemButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const itemName = this.getAttribute('data-item-name');
            const itemPrice = parseFloat(this.getAttribute('data-item-price'));
            const quantityInput = this.closest('.row').querySelector('.quantity-input');
            const quantity = parseInt(quantityInput.value);

            const existingRow = Array.from(summaryTableBody.children).find(row => row
                .querySelector('td:first-child').textContent === itemName);

            if (existingRow) {
                const quantityCell = existingRow.querySelector('td:nth-child(2)');
                quantityCell.textContent = parseInt(quantityCell.textContent) + quantity;
            } else {
                const row = document.createElement('tr');
                row.setAttribute('data-item-id', itemId);
                row.setAttribute('data-item-price', itemPrice);

                row.innerHTML = `
                    <td>${itemName}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${itemPrice.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-danger btn-sm remove-btn">Remove</button>
                    </td>
                `;

                row.querySelector('.remove-btn').addEventListener('click', () => {
                    row.remove();
                    updateRowVisibility();
                    updateTotalAmount();
                });

                summaryTableBody.appendChild(row);
            }

            updateRowVisibility();
            updateTotalAmount();
        });
    });

    showMoreLink.addEventListener('click', (e) => {
        e.preventDefault();
        summaryTableBody.querySelectorAll('tr').forEach(row => row.style.display = '');
        showMoreLink.style.display = 'none';
    });

    const handleQuantityChange = (button, operation) => {
        const quantityInput = button.closest('.row').querySelector('.quantity-input');
        let quantity = parseInt(quantityInput.value);
        if (operation === 'increase') {
            quantity++;
        } else if (operation === 'decrease' && quantity > 1) {
            quantity--;
        }
        quantityInput.value = quantity;

        const itemName = button.closest('.row').querySelector('.text-4xl').textContent.trim();
        const row = Array.from(summaryTableBody.querySelectorAll('tr')).find(row => row.querySelector(
            'td:first-child').textContent === itemName);
        if (row) {
            row.querySelector('td:nth-child(2)').textContent = quantity;
        }

        updateTotalAmount();
    };

    document.querySelectorAll('.increase-btn').forEach(button => {
        button.addEventListener('click', function() {
            handleQuantityChange(this, 'increase');
        });
    });

    document.querySelectorAll('.decrease-btn').forEach(button => {
        button.addEventListener('click', function() {
            handleQuantityChange(this, 'decrease');
        });
    });

    orderForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const items = Array.from(document.querySelectorAll('#summaryItems tr')).map(row => {
            return {
                item_id: row.getAttribute('data-item-id'),
                item_name: row.querySelector('td:nth-child(1)').textContent.trim(),
                quantity: parseInt(row.querySelector('td:nth-child(2)').textContent.trim())
            };
        });

        const kitchenTime = kitchenTimeInput.value;

        if (totalAmount === 0) {
            alert('Please add at least one item to the order.');
            return;
        }

        fetch('/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                },
                body: JSON.stringify({
                    amount: totalAmount.toFixed(2),
                    kitchen_time: kitchenTime,
                    items: items,
                }),
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to process the order');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Order created successfully!');
                    window.location.href = data.redirect_url;
                } else {
                    alert('Failed to create order!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while processing the order.');
            });

    });

    updateRowVisibility();
    updateTotalAmount();
});
</script>
@endsection