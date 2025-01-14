@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Reports';
    });
</script>

@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/report.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush

@section('main')
    <style>
        #dayTransactionReportTable_paginate,
        #dayTransactionReportTable_filter,
        .dataTables_length,
        .dataTables_info {
            margin: 0.5vw 4.2vw !important;
            font-size: 0.8rem !important;
        }

        #dayTransactionReportTable_next,
        #dayTransactionReportTable_previous,
        #dayTransactionReportTable_paginate span a {
            margin: 0.5vw !important;
            padding: 0 !important;
            font-size: 0.8rem
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        table.dataTable tfoot td {
            border-top: none;
        }
    </style>

    <main id="report">
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
            use Carbon\Carbon;
            $branch_id = session('branch_id');
            $totalOrders = $totalOrders;
            if ($totalOrders) {
                $todayDate = Carbon::today()->format('m/d/y');
                $todayTotalSales = 0.0;
                foreach ($totalOrders as $order) {
                    $orderDate = $order->created_at->format('m/d/y');
                    if ($orderDate == $todayDate && $order->status == 1) {
                        $order_bill = (float) str_replace('Rs. ', '', $order->total_bill);
                        $todayTotalSales += $order_bill;
                    }
                }
            }
            $salesmans = $salesman;
        @endphp

        <h2 id="heading">Reports</h2>
        <div id="options">
            <div id="salesman_reports">
                <h4>Salesman Reports</h4>
                {{--  
                |---------------------------------------------------------------|
                |===================== Today's total sales =====================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option1" onclick="todayTotalSales()">
                    Today's total sales
                </button>
                <div id="todayTotalSalesOverlay"></div>
                <div id="todayTotalSales">
                    <h3>Today's Total Sales</h3>
                    <hr>
                    <div class="details" id="detail">
                        <p id = "text-info">Total sales made on all tills on <span id="date">{{ $todayDate }}</span>
                        </p>
                        <p id = "totalSales">Rs. {{ $todayTotalSales }}</p>
                    </div>
                    <div class="btns">
                        <button type="button" onclick="closeTodayTotalSales()">OK</button>
                    </div>
                </div>

                {{--  
                |---------------------------------------------------------------|
                |================= Day full transaction report =================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option2" onclick="dayFullTransactionReport()">
                    Day full transaction report
                </button>
                <div id="dayFullTransactionReportOverlay"></div>
                <form id="dayFullTransactionReport" action="{{ route('dayFullTransactionReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Daily Transaction Report By Salesman</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">
                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="transaction-report-date">Transaction Report Date</label>
                            <input type="date" id="transaction-report-date" name="transaction_report_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="salesman">Select Salesman</label>
                            <select name="salesman" id="salesman" required>
                                <option value="" selected disabled>Select Salesman</option>
                                @foreach ($salesmans as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button class="option-close-btn" id="closebtn" type="button"
                            onclick="closeDailyTransactionReportTable()">Close</button>

                    </div>
                </form>

                @if (session()->has('dailyOrders'))
                    <div id="dayFullTransactionReportTableOverlay"></div>
                    <div id="dayFullTransactionReportTable">
                        <h3>Day full transaction report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Time of Sale</th>
                                        <th>Items</th>
                                        <th>Qty</th>
                                        <th>Product Total Price</th>
                                        <th>Tax</th>
                                        <th>Discount</th>
                                        <th>Total Bill</th>
                                        <th>Payment Methods</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $dailyOrders = session('dailyOrders');
                                        $total_sales = 0.0;
                                        $salesman_id = null;
                                        $selectedDate = null;
                                        $renderedOrders = [];
                                    @endphp
                                    @foreach ($dailyOrders as $value)
                                        @php
                                            if (isset($renderedOrders[$value->order_number])) {
                                                continue;
                                            }

                                            $orderProduct = [];
                                            $productQuantities = [];
                                            $productPrices = [];
                                            foreach ($dailyOrders as $order) {
                                                if ($order->order_number == $value->order_number) {
                                                    foreach ($order->items as $item) {
                                                        $orderProduct[] = $item->product_name;
                                                        $productQuantities[] = $item->product_quantity;
                                                        $productPrices[] = $item->total_price;
                                                    }
                                                }
                                            }
                                            $renderedOrders[$value->order_number] = true;
                                            $salesman_id = $value->salesman_id;
                                            $selectedDate = $value->selected_date;
                                            $order_bill = (float) str_replace('Rs. ', '', $value->total_bill);
                                            $total_sales += $order_bill;
                                            $discountValue = $value->discount === null ? 0 : $value->discount;
                                        @endphp
                                        <tr style="border-bottom: 1px solid #000;">
                                            <td>{{ $value->order_number }}</td>
                                            <td>{{ $value->created_at->format('H:i:s') }}</td>
                                            <td>
                                                <div style="display: flex; flex-direction:column;">
                                                    @foreach ($orderProduct as $product)
                                                        <p class="truncate-text">{{ $product }}</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; flex-direction:column;">
                                                    @foreach ($productQuantities as $quantity)
                                                        <p class="truncate-text">{{ $quantity }}</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; flex-direction:column;">
                                                    @foreach ($productPrices as $price)
                                                        <p class="truncate-text">{{ $price }}</p>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>{{ $value->taxes }}</td>
                                            <td>{{ $value->discount_type == '%' ? (int) $order_bill * (int) ($discountValue / 100) : $value->discount }}
                                            </td>
                                            <td>{{ $value->total_bill }}</td>
                                            <td>{{ $value->payment_method }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="text-align: right;"><strong>Total Sales:</strong></td>
                                        <td>Rs. {{ number_format($total_sales, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printDailyReport', [$branch_id, $salesman_id, $selectedDate]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button" onclick="closeDailyTransactionReportTable()">Close
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('pdf_filename'))
                    <input type="hidden" value="{{ session('pdf_filename') }}" id="pdf_link">
                    <a id="orderRecipt" href="{{ asset('PDF/Reports/' . session('pdf_filename')) }}" download></a>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            let pdfLink = document.getElementById('orderRecipt');
                            pdfLink.style.display = 'block';
                            pdfLink.click();
                            let file_name = document.getElementById('pdf_link').value;
                            route = "{{ route('deleteReportPDF', '_file_name') }}";
                            route = route.replace('_file_name', file_name);
                            window.location.href = route;
                        });
                    </script>
                @endif


                {{--  
                |---------------------------------------------------------------|
                |================ Sales Assistant total report =================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option3" onclick="salesAssistantTotalReport()">
                    Sales assistant total sales report
                </button>
                <div id="salesAssistantTotalReportOverlay"></div>
                <form id="salesAssistantTotalReport" action="{{ route('salesAssistantTotalSalesReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Sales Assistant Total Sales Report</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="report-start-date">Start Date</label>
                            <input type="date" id="report-start-date" name="report_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="report-end-date">End Date</label>
                            <input type="date" id="report-end-date" name="report_end_date" required>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button class="option-close-btn" id="closebtn" type="button"
                            onclick="closeSalesAssistantTotalReport()">Close</button>

                    </div>
                </form>

                @if (session()->has('report'))
                    <div id="salesAssistantTotalReportTableOverlay"></div>
                    <div id="salesAssistantTotalReportTable">
                        <h3>Day full transaction report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Salesman Name</th>
                                        <th>Date</th>
                                        <th>Gross Sales</th>
                                        <th>Gross Refunds</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $report = session('report');
                                    @endphp
                                    @foreach ($report['sales_data'] as $salesman => $dailyReport)
                                        @foreach ($dailyReport as $date => $totals)
                                            <tr>
                                                <td>{{ $salesman }}</td>
                                                <td>{{ $date }}</td>
                                                <td>Rs. {{ $totals['sales'] }}</td>
                                                <td>Rs. {{ $totals['refunds'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="btns">
                            @php
                                $start_date = $report['report_start_date'];
                                $end_date = $report['report_end_date'];
                            @endphp

                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printSalesReport', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button"
                                onclick="closeSalesAssistantTotalReport()">Close</button>
                        </div>
                    </div>
                @endif

                {{--  
                |---------------------------------------------------------------|
                |================ Salesman Reconcilation report ================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option4" onclick="tillReconcilationFigureByDate()">
                    Salesman reconcilation figure by date
                </button>
                <div id="tillReconcilationFigureByDateOverlay"></div>
                <form id="tillReconcilationFigureByDate" action="{{ route('tillReconcilationFigureByDate') }}"
                    method="POST" enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Salesman Reconcilation Figure By Date</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="report-start-date">Start Date</label>
                            <input type="date" id="report-start-date1" name="report_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="report-end-date">End Date</label>
                            <input type="date" id="report-end-date1" name="report_end_date" required>
                        </div>
                        <div class="inputdivs">
                            <label for="salesman">Select Salesman</label>
                            <select name="salesman" id="salesman" required>
                                <option value="" selected disabled>Select Salesman</option>
                                @foreach ($salesmans as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button class="option-close-btn" id="closebtn" type="button"
                            onclick="closeSalesAssistantTotalReport()">Close</button>
                    </div>
                </form>

                @if (session()->has('dailyReconciliation'))
                    <div id="tillReconcilationFigureByDateTableOverlay"></div>
                    <div id="tillReconcilationFigureByDateTable">
                        @php
                            $dailyReconciliation = session('dailyReconciliation');
                        @endphp
                        <h3>Salesman Reconcilation Figure By Date Report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Salesman Name</th>
                                        <th>Date</th>
                                        <th>Cash</th>
                                        <th>Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $dailyReconciliation = session('dailyReconciliation');
                                        $total_Sales = $dailyReconciliation['total_sales'];
                                    @endphp
                                    @foreach ($dailyReconciliation['sales_data'] as $salesman => $dailyReport)
                                        @foreach ($dailyReport as $date => $totals)
                                            <tr>
                                                <td>{{ $salesman }}</td>
                                                <td>{{ $date }}</td>
                                                <td>Rs. {{ $totals['sales'] }}</td>
                                                <td>Rs. {{ $totals['discount'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align: left;"><strong>Total Sales Made by Salesman:</strong></td>
                                        <td>Rs. {{ number_format($total_Sales, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div class="btns">
                            @php
                                $start_date = $dailyReconciliation['report_start_date'];
                                $end_date = $dailyReconciliation['report_end_date'];
                                $salesman = $dailyReconciliation['salesman_id'];
                            @endphp

                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printSalesmanReconcilationReport', [$branch_id, $start_date, $end_date, $salesman]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button"
                                onclick="closeSalesAssistantTotalReport()">Close</button>
                        </div>
                    </div>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |===================== Product Sale Report =====================|
            |---------------------------------------------------------------|
            --}}

            <div id="product_reports">
                <h4>Product Reports</h4>
                <button type="button" class="option" id="option5" onclick="soldProductsReport()">
                    Product sales report
                </button>
                <div id="soldProductsReportOverlay"></div>
                <form id="soldProductsReport" action="{{ route('soldProductsReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Products Sales Report</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">
                    <div class="details" id="detail">

                        <div class="inputdivs">
                            <label for="start-date2">Start Date</label>
                            <input type="date" id="start-date2" name="transaction_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="end-date2">End Date</label>
                            <input type="date" id="end-date2" name="transaction_end_date" required>
                        </div>

                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" class="option-close-btn" type="button"
                            onclick="closesoldProductsReport()">Close</button>
                    </div>
                </form>
                @if (session()->has('dailySales'))
                    <div id="soldProductsReportTableOverlay"></div>
                    <div id="soldProductsReportTable">
                        @php
                            $dailySales = session('dailySales');
                            $total_Sales = 0.0;
                            $count = 0;
                        @endphp
                        <h3>Sold Products Report</h3>
                        <hr>
                        <div id="table-container">
                            @php
                                $groupedItems = [];
                                $total_Sales = 0;
                                $count = 0;

                                // Group items by product_id and sum their quantities and total prices
                                foreach ($dailySales as $Sales) {
                                    foreach ($Sales->items as $item) {
                                        $productId = $item->product_id;
                                        $productName = $item->product_name;
                                        $productPrice = $item->product_price;
                                        $quantity = $item->product_quantity;
                                        $totalPrice = (float) str_replace('Rs. ', '', $item->total_price);
                                        $date = $item->created_at->format('d/m/y');

                                        if (!isset($groupedItems[$productId])) {
                                            $groupedItems[$productId] = [
                                                'product_id' => $productId,
                                                'product_name' => $productName,
                                                'product_price' => $productPrice,
                                                'product_quantity' => 0,
                                                'total_price' => 0,
                                                'date' => $date,
                                            ];
                                        }

                                        $groupedItems[$productId]['product_quantity'] += $quantity;
                                        $groupedItems[$productId]['total_price'] += $totalPrice;
                                        $total_Sales += $totalPrice;
                                        $count += $quantity;
                                    }
                                    $start_date = $Sales->start_date;
                                    $end_date = $Sales->end_date;
                                }
                            @endphp

                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Product Id</th>
                                        <th>Product Name</th>
                                        <th>Product Price</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedItems as $item)
                                        <tr>
                                            <td>{{ $item['product_id'] }}</td>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>{{ $item['product_price'] }}</td>
                                            <td>{{ $item['product_quantity'] }}</td>
                                            <td>Rs. {{ number_format($item['total_price'], 2) }}</td>
                                            <td>{{ $item['date'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="border: none;">
                                        <td><strong>Total Sales</strong></td>
                                        <td style="width: 5px;">:</td>
                                        <td>Rs. {{ number_format($total_Sales, 2) }}</td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td><strong>Total Products Sold</strong></td>
                                        <td style="width: 5px;">:</td>
                                        <td>{{ $count }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printSoldProductsReport', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>
                            <button id="closebtn" type="button" onclick="closesoldProductsReport()">Close</button>
                        </div>
                    </div>
                @endif


                {{-- <button type="button" class="option" id="option6">
                    Product sold sales amount report
                </button> --}}

                {{--  
                |---------------------------------------------------------------|
                |================== Stock History Report =======================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option7"
                    onclick="window.location.href='{{ route('stockHistoryReport', $branch_id) }}'">
                    Stock History report
                </button>

                {{--  
                |---------------------------------------------------------------|
                |================== Products Refund Report =====================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option8" onclick="productsRefundReport()">
                    Products refund report
                </button>
                <div id="productsRefundReportOverlay"></div>
                <form id="productsRefundReport" action="{{ route('productsRefundReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Products Refund Report</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="start-date3">Start Date</label>
                            <input type="date" id="start-date3" name="transaction_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="end-date3">End Date</label>
                            <input type="date" id="end-date3" name="transaction_end_date" required>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" class="option-close-btn" type="button"
                            onclick="closeProductsRefundReport()">Close</button>
                    </div>
                </form>
                @if (session()->has('salesRefund'))
                    <div id="productsRefundReportTableOverlay"></div>
                    <div id="productsRefundReportTable">
                        @php
                            $salesRefund = session('salesRefund');
                            // dd($salesRefund);
                            $total_Sales = 0.0;
                            $count = 0;
                            $start_date = null;
                            $end_date = null;
                        @endphp
                        <h3>Sold Products Report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Transaction Id</th>
                                        <th>Salesman</th>
                                        <th>Product Id</th>
                                        <th>Product Name</th>
                                        <th>Product Price</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesRefund as $Sales)
                                        @php
                                            $start_date = $Sales->start_date;
                                            $end_date = $Sales->end_date;
                                        @endphp
                                        @foreach ($Sales->items as $item)
                                            @php
                                                $date = $item->created_at->format('d/m/y');
                                                $order_bill = (float) str_replace('Rs. ', '', $item->total_price);
                                                $total_Sales += $order_bill;
                                                $count += 1;
                                                $start_date = $Sales->start_date;
                                                $end_date = $Sales->end_date;
                                                $salesman = $Sales->salesman->name;
                                            @endphp
                                            <tr>
                                                <td>{{ $item->order_number }}</td>
                                                <td>{{ $salesman }}</td>
                                                <td>{{ $item->product_id }}</td>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->product_price }}</td>
                                                <td>{{ $item->product_quantity }}</td>
                                                <td>{{ $item->total_price }}</td>
                                                <td>{{ $date }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Refund:</strong></td>
                                        <td>Rs. {{ number_format($total_Sales, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Products
                                                Refund:</strong>
                                        </td>
                                        <td>{{ $count }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printProductsRefundReport', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>
                            <button id="closebtn" type="button" onclick="closeProductsRefundReport()">Close</button>
                        </div>
                    </div>
                @endif

                {{--  
                |---------------------------------------------------------------|
                |======================= Refunds Report ========================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option9" onclick="refundReport()">
                    Refund report
                </button>
                <div id="refundReportOverlay"></div>
                <form id="refundReport" action="{{ route('refundReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Refund Report</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="start-date4">Start Date</label>
                            <input type="date" id="start-date4" name="transaction_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="end-date4">End Date</label>
                            <input type="date" id="end-date4" name="transaction_end_date" required>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" class="option-close-btn" type="button"
                            onclick="closeRefundReport()">Close</button>
                    </div>
                </form>
                @if (session()->has('refundsReport'))
                    <div id="refundReportTableOverlay"></div>
                    <div id="refundReportTable">
                        <h3>Refund Report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Salesman Name</th>
                                        <th>Refund Date</th>
                                        <th>Gross Refunds</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $refundsReport = session('refundsReport');
                                    @endphp
                                    @foreach ($refundsReport['refunds_data'] as $salesman => $refund)
                                        @foreach ($refund as $date => $totals)
                                            <tr>
                                                <td>{{ $salesman }}</td>
                                                <td>{{ $date }}</td>
                                                <td>Rs. {{ $totals['refunds'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Refund:</strong></td>
                                        <td>Rs. {{ $refundsReport['total_refunds'] }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="btns">
                            @php
                                $start_date = $refundsReport['report_start_date'];
                                $end_date = $refundsReport['report_end_date'];
                            @endphp

                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printRefundReport', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button" onclick="closeRefundReport()">Close</button>
                        </div>
                    </div>
                @endif
            </div>

            {{--  
            |---------------------------------------------------------------|
            |==================== Tax Report By Date =======================|
            |---------------------------------------------------------------|
            --}}

            <div id="tax_reports">
                <h4>Tax Reports</h4>
                <button type="button" class="option" id="option10" onclick="taxReportByDate()">
                    Tax report by date
                </button>
                <div id="taxReportByDateOverlay"></div>
                <form id="taxReportByDate" action="{{ route('taxReportByDate') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Tax Report By Date</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="start-date5">Start Date</label>
                            <input type="date" id="start-date5" name="report_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="end-date5">End Date</label>
                            <input type="date" id="end-date5" name="report_end_date" required>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" class="option-close-btn" type="button"
                            onclick="closeTaxReportByDate()">Close</button>
                    </div>
                </form>

                @if (session()->has('taxReport'))
                    <div id="taxReportTableOverlay"></div>
                    <div id="taxReportTable">
                        <h3>Daily Tax Report</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Salesman Name</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Taxes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $taxReport = session('taxReport');
                                    @endphp
                                    @foreach ($taxReport['sales_data'] as $salesman => $tax)
                                        @foreach ($tax as $date => $totals)
                                            <tr>
                                                <td>{{ $salesman }}</td>
                                                <td>{{ $date }}</td>
                                                <td>Rs. {{ $totals['Sale'] }}</td>
                                                <td>Rs. {{ $totals['Tax'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Sales:</strong></td>
                                        <td>Rs. {{ $taxReport['total_sales'] }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Taxes:</strong></td>
                                        <td>Rs. {{ $taxReport['total_tax'] }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="btns">
                            @php
                                $start_date = $taxReport['report_start_date'];
                                $end_date = $taxReport['report_end_date'];
                            @endphp

                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printTaxReportByDate', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button" onclick="closeRefundReport()">Close</button>
                        </div>
                    </div>
                @endif

                {{--  
                |---------------------------------------------------------------|
                |============== Daily Transaction Tax Report ===================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option10" onclick="dailyTransactionTaxReport()">
                    Daily transaction tax report
                </button>
                <div id="dailyTransactionTaxReportOverlay"></div>
                <form id="dailyTransactionTaxReport" action="{{ route('dailyTransactionTaxReport') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Daily Transaction Tax Report</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">

                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="start-date6">Start Date</label>
                            <input type="date" id="start-date6" name="report_start_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="end-date6">End Date</label>
                            <input type="date" id="end-date6" name="report_end_date" required>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" type="button" class="option-close-btn"
                            onclick="closeDailyTransactionTaxReport()">Close</button>
                    </div>
                </form>

                @if (session()->has('transactionTaxes'))
                    <div id="dailyTransactionTaxReportTableOverlay"></div>
                    <div id="dailyTransactionTaxReportTable">
                        <h3>Daily Transaction Tax Report</h3>
                        <hr>
                        @php
                            $transactionTaxes = session('transactionTaxes');
                            $total_Sales = 0.0;
                            $total_Taxes = 0.0;
                        @endphp
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Order Id</th>
                                        <th>Salesman Name</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Taxes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $transactionTaxes = session('transactionTaxes');
                                    @endphp
                                    @foreach ($transactionTaxes as $tax)
                                        @php
                                            $date = $tax->created_at->format('d/m/y');
                                            $order_bill = (float) str_replace('Rs. ', '', $tax->total_bill);
                                            $order_tax = (float) str_replace('Rs. ', '', $tax->taxes);
                                            $total_Sales += $order_bill + $order_tax;
                                            $total_Taxes += $order_tax;
                                            $start_date = $tax->start_date;
                                            $end_date = $tax->end_date;
                                        @endphp
                                        <tr>
                                            <td>{{ $tax->order_number }}</td>
                                            <td>{{ $tax->salesman->name }}</td>
                                            <td>{{ $date }}</td>
                                            <td>Rs. {{ $order_bill + $order_tax }}</td>
                                            <td>Rs. {{ $tax->taxes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Sales:</strong></td>
                                        <td>Rs. {{ $total_Sales }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;"><strong>Total Taxes:</strong></td>
                                        <td>Rs. {{ $total_Taxes }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printDailyTransactionTaxReport', [$branch_id, $start_date, $end_date]) }}'">
                                Print
                            </button>

                            <button id="closebtn" type="button"
                                onclick="closeDailyTransactionTaxReport()">Close</button>
                        </div>
                    </div>
                @endif

                {{--  
                |---------------------------------------------------------------|
                |=========== Daily Transaction By Salesman Report ==============|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option13" onclick="salesmanTaxReportByDate()">
                    {{-- Transaction / User defined sales field report --}}
                    Tax report by salesman
                </button>
                <div id="salesmanTaxReportByDateOverlay"></div>
                <form id="salesmanTaxReportByDate" action="{{ route('salesmanTaxReportByDate') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Tax Report by Salesman</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">
                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="transaction-report-date1">Tax Report Date</label>
                            <input type="date" id="transaction-report-date1" name="transaction_report_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="salesman">Select Salesman</label>
                            <select name="salesman" id="salesman" required>
                                <option value="" selected disabled>Select Salesman</option>
                                @foreach ($salesmans as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" class="option-close-btn" type="button"
                            onclick="closeSalesmanTaxReportByDate()">Close</button>
                    </div>
                </form>

                @if (session()->has('salesmanTaxReport'))
                    <div id="salesmanTaxReportByDateTableOverlay"></div>
                    <div id="salesmanTaxReportByDateTable">
                        <h3>Salesman Tax Report By Date</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Time of Sale</th>
                                        <th>Total Bill</th>
                                        <th>Total Taxes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $salesmanTaxReport = session('salesmanTaxReport');
                                        $salesman_id = null;
                                        $transaction_report_date = null;
                                    @endphp
                                    @foreach ($salesmanTaxReport as $value)
                                        @php
                                            $salesman_id = $value->salesman_id;
                                            $salesmanName = $value->salesman->name;
                                            $transaction_report_date = $value->transaction_date;
                                            $time = $value->created_at->format('H:i:s');
                                        @endphp
                                        <tr>
                                            <td>{{ $value->order_number }}</td>
                                            <td>{{ $time }}</td>
                                            <td>{{ $value->total_bill }}</td>
                                            <td>Rs. {{ $value->taxes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight:bold;">
                                        <td style="padding: 10px 0; width:125px"><strong>Report By &nbsp;&nbsp;&nbsp; :
                                            </strong></td>
                                        <td style="padding: 10px 0;">{{ $salesmanName }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printSalesmanTaxReportByDate', [$branch_id, $salesman_id, $transaction_report_date]) }}'">
                                Print
                            </button>
                            <button id="closebtn" type="button" onclick="closeSalesmanTaxReportByDate()">Close</button>
                        </div>
                    </div>
                @endif

                {{--  
                |---------------------------------------------------------------|
                |=========== Daily sales and discount ==========================|
                |---------------------------------------------------------------|
                --}}

                <button type="button" class="option" id="option14" onclick="salesAndDiscountReportByDate()">
                    Daily sales discount report
                </button>
                <div id="salesAndDiscountReportByDateOverlay"></div>
                <form id="salesAndDiscountReportByDate" action="{{ route('salesAndDiscountReportByDate') }}"
                    method="POST" enctype="multipart/form-data" onsubmit="show_Loader()">
                    @csrf
                    <h3>Sales Discount Report By Date</h3>
                    <hr>
                    <input type="hidden" value="{{ $branch_id }}" name = "branch_id">
                    <div class="details" id="detail">
                        <div class="inputdivs">
                            <label for="transaction-report-date2">Sales Discount Report Date</label>
                            <input type="date" id="transaction-report-date2" name="transaction_report_date" required>
                        </div>

                        <div class="inputdivs">
                            <label for="salesman">Select Salesman</label>
                            <select name="salesman" id="salesman" required>
                                <option value="" selected disabled>Select Salesman</option>
                                @foreach ($salesmans as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="btns">
                        <button type="submit">OK</button>
                        <button id="closebtn" type="button" class="option-close-btn"
                            onclick="closeSalesAndDiscountReportByDate()">Close</button>
                    </div>
                </form>

                @if (session()->has('salesmanDiscountReport'))
                    <div id="salesAndDiscountReportByDateTableOverlay"></div>
                    <div id="salesAndDiscountReportByDateTable">
                        <h3>Salesman Tax Report By Date</h3>
                        <hr>
                        <div id="table-container">
                            <table id="dayTransactionReportTable">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Time of Sale</th>
                                        <th>Salesman</th>
                                        <th>Sale</th>
                                        <th>Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $salesmanDiscountReport = session('salesmanDiscountReport');
                                        $salesman_id = null;
                                        $transaction_report_date = null;
                                    @endphp
                                    @foreach ($salesmanDiscountReport as $value)
                                        @php
                                            $salesman_id = $value->salesman_id;
                                            $transaction_report_date = $value->transaction_date;
                                            $time = $value->created_at->format('H:i:s');
                                            $bill = (float) str_replace('Rs. ', '', $value->total_bill);
                                        @endphp
                                        <tr>
                                            <td>{{ $value->order_number }}</td>
                                            <td>{{ $time }}</td>
                                            <td>{{ $value->salesman->name }}</td>
                                            <td>Rs. {{ $value->total_bill }}</td>
                                            <td>Rs.
                                                {{ $value->discount_type == '%' ? (float) $bill * (float) ($value->discount / 100) : $value->discount }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="btns">
                            <button id="printbtn" type="button"
                                onclick="window.location.href = '{{ route('printSalesAndDiscountReportByDate', [$branch_id, $salesman_id, $transaction_report_date]) }}'">
                                Print
                            </button>
                            <button id="closebtn" type="button"
                                onclick="closeSalesAndDiscountReportByDate()">Close</button>
                        </div>
                    </div>
                @endif

                {{-- <button type="button" class="option" id="option15">
                Item price change / Discounts
                </button> --}}
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dayTransactionReportTable').DataTable({
                "paging": true,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

        function todayTotalSales() {
            document.getElementById('todayTotalSalesOverlay').style.display = 'block';
            document.getElementById('todayTotalSales').style.display = 'flex';
        }

        function closeTodayTotalSales() {
            document.getElementById('todayTotalSalesOverlay').style.display = 'none';
            document.getElementById('todayTotalSales').style.display = 'none';
        }

        function dayFullTransactionReport() {
            document.getElementById('dayFullTransactionReportOverlay').style.display = 'block';
            document.getElementById('dayFullTransactionReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transaction-report-date').setAttribute('max', today);;
        }

        function closeDailyTransactionReportTable() {
            document.getElementById('dayFullTransactionReportOverlay').style.display = 'none';
            document.getElementById('dayFullTransactionReport').style.display = 'none';
            window.location.reload();
        }

        function salesAssistantTotalReport() {
            document.getElementById('salesAssistantTotalReportOverlay').style.display = 'block';
            document.getElementById('salesAssistantTotalReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('report-start-date').setAttribute('max', today);
            document.getElementById('report-end-date').setAttribute('max', today);
        }

        function closeSalesAssistantTotalReport() {
            document.getElementById('salesAssistantTotalReportOverlay').style.display = 'none';
            document.getElementById('salesAssistantTotalReport').style.display = 'none';
            window.location.reload();
        }

        function tillReconcilationFigureByDate() {
            document.getElementById('tillReconcilationFigureByDateOverlay').style.display = 'block';
            document.getElementById('tillReconcilationFigureByDate').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('report-start-date1').setAttribute('max', today);
            document.getElementById('report-end-date1').setAttribute('max', today);
        }

        function closeTillReconcilationFigureByDate() {
            document.getElementById('tillReconcilationFigureByDateOverlay').style.display = 'none';
            document.getElementById('tillReconcilationFigureByDate').style.display = 'none';
            window.location.reload();
        }

        function soldProductsReport() {
            document.getElementById('soldProductsReportOverlay').style.display = 'block';
            document.getElementById('soldProductsReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start-date2').setAttribute('max', today);
            document.getElementById('end-date2').setAttribute('max', today);
        }

        function closesoldProductsReport() {
            document.getElementById('soldProductsReportOverlay').style.display = 'none';
            document.getElementById('soldProductsReport').style.display = 'none';
            window.location.reload();
        }

        function productsRefundReport() {
            document.getElementById('productsRefundReportOverlay').style.display = 'block';
            document.getElementById('productsRefundReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start-date3').setAttribute('max', today);
            document.getElementById('end-date3').setAttribute('max', today);
        }

        function closeProductsRefundReport() {
            document.getElementById('productsRefundReportOverlay').style.display = 'none';
            document.getElementById('productsRefundReport').style.display = 'none';
            window.location.reload();
        }

        function refundReport() {
            document.getElementById('refundReportOverlay').style.display = 'block';
            document.getElementById('refundReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start-date4').setAttribute('max', today);
            document.getElementById('end-date4').setAttribute('max', today);
        }

        function closeRefundReport() {
            document.getElementById('refundReportOverlay').style.display = 'none';
            document.getElementById('refundReport').style.display = 'none';
            window.location.reload();
        }

        function taxReportByDate() {
            document.getElementById('taxReportByDateOverlay').style.display = 'block';
            document.getElementById('taxReportByDate').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start-date5').setAttribute('max', today);
            document.getElementById('end-date5').setAttribute('max', today);
        }

        function closeTaxReportByDate() {
            document.getElementById('taxReportByDateOverlay').style.display = 'none';
            document.getElementById('taxReportByDate').style.display = 'none';
            window.location.reload();
        }

        function dailyTransactionTaxReport() {
            document.getElementById('dailyTransactionTaxReportOverlay').style.display = 'block';
            document.getElementById('dailyTransactionTaxReport').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start-date6').setAttribute('max', today);
            document.getElementById('end-date6').setAttribute('max', today);
        }

        function closeDailyTransactionTaxReport() {
            document.getElementById('dailyTransactionTaxReportOverlay').style.display = 'none';
            document.getElementById('dailyTransactionTaxReport').style.display = 'none';
            window.location.reload();
        }

        function salesmanTaxReportByDate() {
            document.getElementById('salesmanTaxReportByDateOverlay').style.display = 'block';
            document.getElementById('salesmanTaxReportByDate').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transaction-report-date1').setAttribute('max', today);
        }

        function closeSalesmanTaxReportByDate() {
            document.getElementById('salesmanTaxReportByDateOverlay').style.display = 'none';
            document.getElementById('salesmanTaxReportByDate').style.display = 'none';
            window.location.reload();
        }

        function salesAndDiscountReportByDate() {
            document.getElementById('salesAndDiscountReportByDateOverlay').style.display = 'block';
            document.getElementById('salesAndDiscountReportByDate').style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('transaction-report-date2').setAttribute('max', today);
        }

        function closeSalesAndDiscountReportByDate() {
            document.getElementById('salesAndDiscountReportByDateOverlay').style.display = 'none';
            document.getElementById('salesAndDiscountReportByDate').style.display = 'none';
            window.location.reload();
        }
    </script>
@endsection
