<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
        margin: 0;
        box-sizing: border-box;
        font-family: "VT323", monospace;
        color: #111;
        font-weight: bold;
    }

    .container {
        background: #f1f1f1;
        padding: 20px 10px;
        font-size: 12px;
    }

    .receipt {
        width: 325px;
        background: #fff;
        margin: 0 auto;
        box-shadow: 5px 5px 19px #ccc;
        padding: 10px;
    }

    .logo {
        text-align: center;
        padding: 5px;
    }

    .logo img {
        height: 75px;
        widows: 75px;
    }

    .address,
    .detail {
        text-align: left;
    }

    .transactionDetails {
        font-size: 12px;
    }

    .transactionDetails table {
        padding-bottom: 05px;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table thead tr th,
    table tbody tr td {
        padding: 2px;
        text-align: center;
    }

    .feedback {
        margin: 10px auto;
        text-align: center;
    }

    .feedback .break,
    .feedback .center {
        font-size: 18px;
        margin: 0
    }

    .feedback .center {
        font-size: 12px;
    }

    .feedback .clickBait {
        font-size: 12px;
        /* margin: 05px 0; */
    }

    .feedback .web {
        font-size: 15px;
        /* margin: 05px 0; */
    }

    .fbr {
        height: 125px;
    }

    .fbvInvoice {
        margin: 15px 0;
        text-align: center;
    }

    .fbrImages {
        display: block;
        text-align: center;
    }

    .fbrLogo {
        margin-right: 10px
    }

    .fbrQR {
        margin-left: 10px
    }

    img {
        width: 75px;
        height: 75px;
    }
</style>

<body>
    @php
        date_default_timezone_set('Asia/Karachi');
        $orderData = $orderData;
        $date_time = $orderData->created_at;
        $date = date('M d, Y', strtotime($date_time));
        $time = date('g:i A', strtotime($date_time));

        $subtotal = 0.0;
    @endphp

    <div id="showScroll" class="container">
        <div class="receipt">
            <h1 class="logo">Crust House</h1>

            {{-- <div class="logo">
                <img src="{{ asset('Images/image 1.png') }}" alt="Pizza Image">
            </div> --}}

            <div class="address">Address :{{ $orderData->salesman->branch->branch_address }}</div>
            <br>
            <div class="detail">
                <p style="display:inline-block; width:62%;">
                    Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $date }}</p>
                <p style="display:inline-block; width:35%;">Time : {{ $time }}</p>
            </div>
            {{-- <div class="detail"> Time : {{ $time }}</div> --}}
            <div class="detail">
                <p style="display:inline-block; width:62%;;">ORDER TYPE &nbsp;:
                    {{ $orderData->ordertype }}</p>
                <p style="display:inline-block; width:35%;">Order:
                    {{ $orderData->order_number }}</p>
            </div>

            {{-- <div class="detail"> ORDER TYPE: {{ $orderData->ordertype }}</div> --}}
            <div class="detail cashier">ASSISTED BY&nbsp;: {{ $orderData->salesman->name }}</div>
            <div class="detail ">PAYMENT MTD&nbsp;: {{ $orderData->payment_method }}</div>

            {{-- <div class="detail ordernumber">Order #:{{ $orderData->order_number }} </div> --}}
            <div class="detail" style="text-align: center; margin-top:10px;">Free Delivery</div>

            <div class="transactionDetails">
                <table>
                    <thead>
                        <tr>
                            <th>Sr.#</th>
                            <th style="width: 180px;">ITEM NAME</th>
                            <th>QTY</th>
                            <th>PRICE(Rs)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $i => $item)
                            @php
                                preg_match('/\d+(\.\d+)?/', $item->total_price, $matches);
                                $numericPart = $matches[0];
                                $subtotal += $numericPart;

                            @endphp
                            <tr>
                                <td style="text-align: left">{{ $i + 1 }}.</td>
                                @if ($item->addons)
                                    <td style="text-align: left">{{ $item->product_name }} with {{ $item->addons }}</td>
                                @else
                                    <td style="text-align: left">{{ $item->product_name }}</td>
                                @endif
                                <td>{{ $item->product_quantity }}</td>
                                <td style="text-align: left">{{ $numericPart }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="detail" style="text-align: center; margin-bottom:15px;"> Recipt #
                {{ $orderData->order_number }} </div> --}}
            <div class="detail">
                <p style="display:inline-block; width:45%;">ORIGINAL AMOUNT &nbsp;&nbsp;:</p>
                <p style="display:inline-block;">Rs. {{ $subtotal }}</p>
            </div>

            @php
                $taxPercent = round(( $orderData->taxes * 100 ) / $subtotal);
            @endphp

            <div class="detail">
                <p style="display:inline-block; width:45%;">TAX  APPLIED &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p>
                <p style="display:inline-block;">Rs. {{ $orderData->taxes}} ({{$taxPercent}}%)</p>
            </div>
            {{-- <div class="detail">TAXES: {{ $orderData->taxes }}</div> --}}
            @if ($orderData->discount != 0)
                <div class="detail">
                    <p style="display:inline-block; width:45%;">DISCOUNT APPLIED &nbsp;:</p>
                    <p style="display:inline-block;">
                        {{ $orderData->discount_type == '%' ? $orderData->discount . '%' : 'Rs ' . $orderData->discount }}
                    </p>
                </div>
            @endif
            <div class="detail" style=" display: block; width:100%;">
                <p style="display:inline-block; width:70%;">TOTAL AMOUNT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p>
                <p style="display:inline-block;">
                    Rs.{{ $orderData->discount_type == '%' ? $subtotal - ($orderData->discount / 100) * $subtotal + $orderData->taxes : $subtotal + $orderData->taxes - $orderData->discount }}
                </p>
            </div>
            <div class="detail">
                <p style="display:inline-block; width:45%;">CASH TENDERED
                    &nbsp;&nbsp;&nbsp;&nbsp;:</p>
                <p style="display:inline-block;">Rs. {{ $orderData->received_cash }}</p>
            </div>
            <div class="detail">
                <p style="display:inline-block; width:45%;">BALANCE
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</p>
                <p style="display:inline-block;">Rs. {{ $orderData->return_change}}</p>
            </div>
            {{-- <div class="returnPolicy bold">
                            Returns with receipt, subject to CVS Return Policy, thru 08/30/2024
                            Refund amount is based on the price after all coupons and discounts.
                        </div> --}}

            <div class="feedback">
                <div class="break">
                    **************************
                </div>
                <p class="center">
                    @if ($orderData->salesman->branch->receipt_message !== null)
                        {{ $orderData->salesman->branch->receipt_message }}
                    @else
                        THANK YOU FOR YOUR VISIT
                    @endif
                </p>

                <h3 class="clickBait">
                    @if ($orderData->salesman->branch->branch_web_address !== null)
                        {{ $orderData->salesman->branch->branch_web_address }}@endif
                </h3>

                <h4 class="web">
                    @if ($orderData->salesman->branch->feedback !== null)
                        {{ $orderData->salesman->branch->feedback }}@endif
                </h4>

                <p class="center">
                    @if ($orderData->salesman->branch->receipt_tagline !== null)
                        {{ $orderData->salesman->branch->receipt_tagline }}@endif
                </p>
                <div class="break">
                    **************************
                </div>
            </div>
            {{-- <div class="fbr">
                <div class="fbvInvoice">
                    <p>FBR INVOICE #:</p>
                </div>
                <div class="fbrImages">

                    <img class="fbrLogo" src="{{ asset('Images/fbrLogo.jpg') }}" alt="FBR POS LOGO">

                    <img class="fbrQR" src="{{ asset('Images/QR Code.png') }}" alt="FBR POS LOGO">

                </div>
            </div> --}}
        </div>
    </div>
</body>

</html>
