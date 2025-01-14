@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Stock History';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/stock.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
<style>
    #stocksHistroyTable_paginate,
    #stocksHistroyTable_filter,
    .dataTables_length,
    .dataTables_info {
        margin: 0.5vw 7vw !important;
        font-size: 0.8rem !important;
    }

    #stocksHistroyTable_next,
    #stocksHistroyTable_previous,
    #stocksHistroyTable_paginate span a {
        margin: 0.5vw !important;
        padding: 0 !important;
        font-size: 0.8rem
    }

    table.dataTable {
        border-collapse: collapse !important;
    }
</style>
@section('main')
    <main id="stock">
        <div style="display:flex;justify-content:space-between; align-item:center;">
            <button id="back" type="button" onclick="showLoader('{{ route('viewStockPage', [$user_id, $branch_id]) }}')">Back</button>
            <h3 style="margin:0; width:60%;">Stock history</h3>
        </div>
        @php
            $stockHistory = $stockHistory;
        @endphp

        <table id="stocksHistroyTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Minimum Quantity</th>
                    <th>Unit price</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockHistory as $stock)
                    @php
                        $date_time = $stock->created_at;
                        $date = date('F d, Y', strtotime($date_time));
                        $time = date('g:i A', strtotime($date_time));
                    @endphp

                    <tr>
                        <td>{{ $stock->itemName }}</td>
                        <td>{{ $stock->itemQuantity }}</td>
                        <td>{{ $stock->mimimumItemQuantity }}</td>
                        <td>{{ $stock->unitPrice }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#stocksHistroyTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });
    </script>
@endsection
