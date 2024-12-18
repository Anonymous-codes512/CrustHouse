<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Screen</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            user-select: none;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .popup {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            position: relative;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 85%;
            height: 85%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            overflow: auto;
        }

        .popup h2 {
            margin: 5px;
        }

        .popup p {
            font-size: 14px;
            color: #555;
            margin: 5px;
        }

        .popup table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .popup table th,
        .popup table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .popup table th {
            background-color: #CACACAFF;
            color: #333;
        }

        .popup table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .popup table tr:hover {
            background-color: #f1f1f1;
        }

        .popup .assign_order {
            display: inline-block;
            padding: 10px 15px;
            background-color: #19985FFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup .assign_order:hover {
            background-color: #1a4b36;
        }

        .bx-arrow-back {
            margin-right: 20rem;
            background-color: #ff4757;
            color: #fff;
            font-size: 1.5rem;
            border-radius: 50%;
            padding: .25rem;
            cursor: pointer;
        }

        .bx-arrow-back:hover {
            background-color: #BC343FFF;
        }
    </style>
</head>

<body>

    <div id="popupOverlay" class="overlay">
        <div class="popup">
            <div style="display: flex;width:90%;align-items:center;">
                <a style="text-decoration: none;"
                    href="{{ route('salesman_dashboard', [$Salesman_id, $Branch_id]) }}"><i
                        class='bx bx-arrow-back'></i></a>
                <h2>Select the Rider for Assigning Order</h2>
            </div>
            <p>Available rider's list is shown below.</p>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>Rider ID</th>
                        <th>Rider Name</th>
                        <th>License Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Riders as $rider)
                        @if ($rider->rider->status === 1)
                            <tr>
                                <td>{{ $rider->rider->id }}</td>
                                <td>{{ $rider->name }}</td>
                                <td>{{ $rider->rider->license_number }}</td>
                                <td>
                                    <a href="{{route('assignOrder', [$Branch_id, $rider->rider->id, $Order_id, $Salesman_id])}}">
                                        <button class="assign_order">Assign</button>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
