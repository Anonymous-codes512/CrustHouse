@extends('Components.Chef')

@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Chef/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Chef - Dashboard';
    });
</script>


<style>
    #newOrdersTable_paginate,
    #newOrdersTable_filter,
    #completedOrdersTable_paginate,
    #completedOrdersTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5rem !important;
        font-size: 0.9rem !important;
    }

    #newOrdersTable_next,
    #newOrdersTable_previous,
    #newOrdersTable_paginate span a,
    #completedOrdersTable_next,
    #completedOrdersTable_previous,
    #completedOrdersTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="chef">
        <div id="pending" class="orderdiv">
            <div class="title">
                <h3>New Orders</h3>
            </div>

            <div class="ordersTable">
                <table id="newOrdersTable">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Total Bill</th>
                            <th>Order type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newOrders as $order)
                            <tr>
                                <td onclick="showDetails({{ json_encode($order) }})">{{ $order->order_number }}</td>
                                <td onclick="showDetails({{ json_encode($order) }})">{{ $order->total_bill }}</td>
                                <td onclick="showDetails({{ json_encode($order) }})">{{ $order->ordertype }}</td>
                                <td>
                                    <a id="done" onclick="showLoader('{{ route('orderComplete', $order->id) }}')" style="cursor: pointer;">Done</a>
                                    <a id="print" href="{{ route('printChefRecipt', $order->id) }}">Print</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="completed" class="orderdiv">
            <div class="title">
                <h3>Completed Orders</h3>
            </div>

            <div class="ordersTable">
                <table id="completedOrdersTable">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Total Bill</th>
                            <th>Order type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($completeOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->total_bill }}</td>
                                <td>{{ $order->ordertype }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="orderItemsOverlay"></div>
        <div id="orderItems">
            <div class="table-container">
                <table id="itemtable" cellpadding="10" class="table">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="tablebody">
                    </tbody>
                </table>
            </div>
            <div class="btns">
                <button id="closebtn" type="button" onclick="closeDetails()">Close</button>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#newOrdersTable').DataTable({
                "paging": true,
                "lengthMenu": [10, 25, 50, 100]
            });
        });

        $(document).ready(function() {
            $('#completedOrdersTable').DataTable({
                "paging": true,
                "lengthMenu": [10, 25, 50, 100]
            });
        });

        function showDetails(order) {
            document.getElementById('orderItemsOverlay').style.display = 'block';
            document.getElementById('orderItems').style.display = 'flex';
            let tablebody = document.getElementById('tablebody');
            tablebody.innerHTML = '';
            order.items.forEach(item => {
                let table_row = document.createElement('tr');

                let table_row_data_1 = document.createElement('td');
                table_row_data_1.textContent = item.order_number;

                let table_row_data_2 = document.createElement('td');
                table_row_data_2.textContent = item.product_name;

                let table_row_data_3 = document.createElement('td');
                table_row_data_3.textContent = item.product_quantity;

                let table_row_data_4 = document.createElement('td');
                table_row_data_4.textContent = item.total_price;

                table_row.appendChild(table_row_data_1);
                table_row.appendChild(table_row_data_2);
                table_row.appendChild(table_row_data_3);
                table_row.appendChild(table_row_data_4);
                tablebody.appendChild(table_row);
            });
        }

        function closeDetails() {
            document.getElementById('orderItemsOverlay').style.display = 'none';
            document.getElementById('orderItems').style.display = 'none';
        }

        let interval = 60000;
        let remainingTime = interval;
        async function fetchData() {
            try {
                // const response = await fetch("http://192.168.1.108:7000/chef/getOrderNotification");
                // const response = await fetch("http://192.168.1.108:8000/chef/getOrderNotification");
                // const response = await fetch("http://192.168.100.7:8000/chef/getOrderNotification");
                // const response = await fetch("http://192.168.100.7:7000/chef/getOrderNotification");
                const response = await fetch("http://127.0.0.1:8000/chef/getOrderNotification");
                const data = await response.json();

                if (data.status === 'success') {
                    show_Loader();
                    window.location.reload();   
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function startCountdown() {
            updateCountdown();
            setInterval(() => {
                if (remainingTime <= 0) {
                    console.log('Fetching data...');
                    fetchData();
                    remainingTime = interval;
                }
                remainingTime -= 1000;
                if (remainingTime <= 0) {
                    fetchData();
                    remainingTime = interval;
                }
                updateCountdown();
            }, 1000);
        }

        function updateCountdown() {
            const seconds = Math.ceil(remainingTime / 1000);
        }
        startCountdown();
    </script>
@endsection
