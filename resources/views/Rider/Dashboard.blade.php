@extends('Components/Rider')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Rider - Dashboard';
    });
</script>

@section('main')
    @php
        $rider_id = $rider_id;
        $branch_id = $branch_id;
        $branch = $branch;
        $branch_Name = 'Dashboard';
        if ($branch) {
            $branch_Name = $branch->branch_city . ' - Dashboard';
        }

        if ($orders) {
            $totalEarnings = $orders
                ->filter(function ($order) {
                    return $order->delivery_status == 1;
                })
                ->sum(function ($order) {
                    $normalizedBill = preg_replace('/[^\d]/', '', $order->total_bill);
                    return (float) $normalizedBill;
                });
        } else {
            $totalEarnings = 0; // Default to zero if no orders
        }

        $pendingOrders = $orders->where('delivery_status', '0')->count();
        $completedOrders = $orders->where('delivery_status', '1')->count();

        $todaysOrders = $orders
            ->filter(function ($order) {
                return \Carbon\Carbon::parse($order->updated_at)->isToday();
            })
            ->count();
    @endphp

    <main class="dashboard-content">
        <h2>{{ $branch_Name }}</h2>
        <section class="dashboard-cards">
            <div class="card">
                <h3>Today's Orders</h3>
                <p class="count" id="todaysOrders">{{ $todaysOrders }}</p>
            </div>
            <div class="card">
                <h3>Completed Orders</h3>
                <p class="count" id="completedOrders">{{ $completedOrders }}</p>
            </div>
            <div class="card">
                <h3>Pending Orders</h3>
                <p class="count" id="pendingOrders">{{ $pendingOrders }}</p>
            </div>
            <div class="card">
                <h3>Total Earnings</h3>
                <p class="count earnings" id="totalEarnings">Rs. {{ $totalEarnings }}</p>
            </div>
        </section>

        <!-- Recent Orders Table -->
        <section class="recent-orders">
            <div class="recent-order-heading">
                <h2>Recent Orders</h2>
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
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @if (
                            $order->delivery_status == 0 &&
                                \Carbon\Carbon::parse($order->updated_at)->greaterThanOrEqualTo(\Carbon\Carbon::now()->subDays(2)))
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customers->name }}</td>
                                <td>{{ $order->order_address }}</td>
                                <td>{{ $order->delivery_status === 0 ? 'Pending' : 'Complete' }}</td>
                                <td>Rs. {{ $order->total_bill }}</td>
                                <td>
                                    <a onclick="showLoader('{{ route('viewOrderDetails', [$order->order_number, $rider_id]) }}')"
                                        title="View Order Details"><i class="bi bi-view-list"></i></a>
                                    <a onclick="showLoader('{{ route('deliveryCancelled', [$order->order_number, $rider_id]) }}')"
                                        title="Mark As Cancelled Order"><i class="bi bi-x-square-fill"></i></a>
                                    <a onclick="showLoader('{{ route('deliveryCompleted', [$order->order_number, $rider_id]) }}')"
                                        title="MarK as Completed Order"><i class="bi bi-check-square-fill"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </section>
        @if (session('order_details'))
            @php
                $order_details = session('order_details');
            @endphp
            <div id="popupOverlay" class="overlay">
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
