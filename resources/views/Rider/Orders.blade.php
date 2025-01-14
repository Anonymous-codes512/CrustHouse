@extends('Components/Rider')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Rider - Orders';
    });
</script>
@php
    $rider_id = $rider_id;
    $branch_id = $branch_id;
    $branch_Name = 'Orders';
    if ($branch) {
        $branch_Name = $branch->branch_city . ' - Orders';
    }
@endphp

@section('main')
    <main class="dashboard-content">
        <h2>{{ $branch_Name }}</h2>
        <section class="recent-orders">
            <div class="recent-order-heading">
                <h2>Orders History</h2>
                <div class="search_bar_div">
                    <input type="text" id="search_bar" name="search" placeholder="Search by Order Id..."
                        style="background-image: url('{{ asset('Images/search.png') }}');">
                </div>
            </div>
            <table id="orders_table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customers->name ?? 'Unknown Customer' }}</td>
                            <td>{{ $order->order_address }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->updated_at)->format('d-m-Y') }}</td>
                            <td>
                                @if ($order->delivery_status === -1)
                                    Cancel
                                @elseif($order->delivery_status === 0)
                                    Pending
                                @elseif($order->delivery_status === 1)
                                    Complete
                                @endif
                            </td>

                            <td>Rs. {{ $order->total_bill }}</td>
                            <td>
                                <a onclick="showLoader('{{ route('viewOrderDetails', [$order->order_number, $rider_id]) }}')"
                                    title="View Order Details">
                                    <i class="bi bi-view-list"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @if (session('order_details'))
            @php
                $order_details = session('order_details');
            @endphp
            <div id="popupOverlay" class="overlay"></div>
            <div class="popup">
                <h2>Selected Order Details</h2>
                <!-- Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Order Items</th>
                            <th>Quantity</th>
                            <th>Total Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="{{ $order_details->items->count() }}">{{ $order_details->order_number }}</td>
                            <td>
                                {{ $order_details->items[0]->product_name }}
                            </td>
                            <td>
                                {{ $order_details->items[0]->product_quantity }}
                            </td>
                            <td rowspan="{{ $order_details->items->count() }}">{{ $order_details->total_bill }}</td>
                        </tr>

                        @foreach ($order_details->items->skip(1) as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->product_quantity }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <button onclick="reload()">Close</button>
            </div>
        @endif
    </main>
    <script>
        function reload() {
            show_Loader();
            window.location.reload();
        }

        document.getElementById('search_bar').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#orders_table tbody tr');

            rows.forEach(row => {
                const orderNumber = row.querySelector('td:first-child').textContent
                    .toLowerCase();
                if (orderNumber.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection
