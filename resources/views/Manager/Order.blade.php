@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Order';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/order.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
<style>
    #ordersTable_paginate,
    #ordersTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 4.1vw !important;
        font-size: 0.8rem !important;
    }

    #ordersTable_next,
    #ordersTable_previous,
    #ordersTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="order">
        @if (session('success'))
            <div id="success" class="alert alert-success">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('success').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('success').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if (session('error'))
            <div id="error" class="alert alert-danger">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('error').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('error').style.display = 'none';
                }, 3000);
            </script>
        @endif
        @php
            $order_id = 0;
            $user_id = session('id');
            $branch_id = session('branch_id');
        @endphp
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Order id</th>
                    <th>Order Number</th>
                    <th>Total bill</th>
                    <th>Salesman</th>
                    {{-- <th>Chef</th> --}}
                    <th>Status</th>
                    {{-- <th>Reason</th> --}}
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="table-row-data">
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->total_bill }}</td>
                        <td>{{ $order->salesman->name }}</td>

                        @if ($order->status == 0)
                            <td class="status">Request for cancellation</td>
                        @elseif ($order->status == 1)
                            <td class="status">Completed</td>
                        @elseif ($order->status == 2)
                            <td class="status">Pending</td>
                        @elseif ($order->status == 3)
                            <td class="status">Canceled</td>
                        @elseif ($order->status == 4)
                            <td class="status">Send to chef</td>
                        @elseif ($order->status == 5)
                            <td class="status">order ready</td>
                        @endif

                        {{-- @if ($order->status == 0)
                        <td>{{$order->cancellation_reason}}</td>
                        @else
                        <td>Nill</td>
                        @endif --}}
                        <td>
                            <a id="view" onclick="showLoader('{{ route('viewOrderProducts', [$branch_id, $order->id]) }}')">View</a>
                            @if ($order->status == 0)
                                <a id="cancel-order" onclick="showLoader('{{ route('cancelorder', [$order->id, $user_id]) }}')">Cancel</a>
                            @elseif ($order->status == 1)
                                <a id="cancel-order" style="background-color:#4d4d4d; cursor: default;">Cancel</a>
                            @elseif($order->status == 2)
                                <a id="cancel-order" onclick="showLoader('{{ route('cancelorder', [$order->id, $user_id]) }}')">Cancel</a>
                            @elseif($order->status == 3)
                                <a id="cancel-order" style="background-color:#4d4d4d;  cursor: default;">Cancel</a>
                            @elseif($order->status == 4)
                                <a id="cancel-order" onclick="showLoader('{{ route('cancelorder', [$order->id, $user_id]) }}')">Cancel</a>
                            @elseif($order->status == 5)
                                <a id="cancel-order" onclick="showLoader('{{ route('cancelorder', [$order->id, $user_id]) }}')">Cancel</a>
                            @endif

                            @if ($order->status == 0)
                                <a id="view" style="cursor: pointer;" onclick="viewReason('{{$order->cancellation_reason}}')">View Reason</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="showCancellationReasonOverlay"></div>
        <div id="showCancellationReason">
            <h2>Reason for Order Cancellation.</h2>
            <p id="reason">Customer reject the order due to bad food taste.</p>
            <div id="btn-div">
                <button onclick="closeCancellationReason()">Close</button>
            </div>
        </div>

        @if ($orderItems != null)
            <div id="orderItemsOverlay"></div>
            <div id="orderItems">
                <div class="table">
                    <table id="itemtable" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $item)
                                @php
                                    $order_id = $item->order_id;
                                @endphp
                                <tr>
                                    <td>{{ $item->order_number }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_quantity }}</td>
                                    <td>{{ $item->total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="btns">
                    <a href="{{ route('printrecipt', $order_id) }}"><button id="printbtn"
                            type="button">Print</button></a>
                    <a onclick="showLoader('{{ route('viewOrdersPage', [$user_id, $branch_id]) }}')"><button id="closebtn"
                            type="button">Close</button></a>
                </div>
            </div>
        @endif

    </main>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#ordersTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [
                    [0, "desc"]
                ]

            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            let orderStatus = document.getElementsByClassName('status');
            Array.from(orderStatus).forEach(status => {
                let statusText = status.textContent.toLowerCase().trim();
                if (statusText === "pending") {
                    status.style.color = '#ff9900';
                } else if (statusText === "completed") {
                    status.style.color = '#35b65b';
                } else if (statusText === "canceled" || statusText === "cancelled") {
                    status.style.color = '#F45B69';
                }
            });
        });

        function viewReason(cancellation_reason){
            document.getElementById('showCancellationReasonOverlay').style.display = 'block';
            document.getElementById('showCancellationReason').style.display = 'flex';
            document.getElementById('reason').textContent = cancellation_reason;
        }

        function closeCancellationReason(cancellation_reason){
            document.getElementById('showCancellationReasonOverlay').style.display = 'none';
            document.getElementById('showCancellationReason').style.display = 'none';
        }
    </script>

@endsection
