<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
</head>

<style>
    #dayFullTransactionReportTable {
        display: flex;
        flex-direction: column;
        background-color: #ffffff;
        border-radius: 10px;
        width: 95vw;
        margin: auto
    }

    #table-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin: auto;
        max-height: 70vh;
        overflow-y: auto;
        scrollbar-width: none;
        padding: 10px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin: 0.5vw auto 2vw;
    }

    table thead tr {
        border-bottom: 2px solid #000;
    }

    table tbody tr {
        border-bottom: 1px solid #898989;
    }

    table tbody tr td {
        text-align: center;
        border-radius: 6px;
        padding: 0.5vw;
        font-size: 1.3vw;
    }
</style>

<body>
    <div id="dayFullTransactionReportTable">
        <h3 style="text-align: center;">
            Daily Tax Report
        </h3>
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
                        <td colspan="2">Rs. {{ $taxReport['total_sales'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><strong>Total Taxes:</strong></td>
                        <td colspan="2">Rs. {{ $taxReport['total_tax'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>


</html>
 