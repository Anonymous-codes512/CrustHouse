<!DOCTYPE html>
<html>

<head>
    <title>Your Order Summary</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0"
        style="margin: 20px auto; background-color: #ffffff; max-width: 600px; border: 1px solid #e0e0e0; border-radius: 10px;">
        <tr>
            <td
                style="background-color: #ffbb00; padding: 20px; text-align: center; color: white; border-radius: 10px 10px 0 0;">
                <h1 style="margin: 0; color:#ffffff">Order Confirmation</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <h2 style="color: #333;">{{ $orderData->customers->name }}, thank you for your order!</h2>
                <p style="color: #555;">Your order has been successfully placed. Below are the details of your order:
                </p>

                <h3 style="color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Order Information</h3>
                <p style="margin: 5px 0; color: #555;"><strong>Phone:</strong> {{ $orderData->customers->phone_number }}
                </p>
                <p style="margin: 5px 0; color: #555;"><strong>Email:</strong> {{ $orderData->customers->email }}</p>
                <p style="margin: 5px 0; color: #555;"><strong>Address:</strong> {{ $orderData->order_address }}</p>
                <p style="margin: 5px 0; color: #555;"><strong>Payment Method:</strong> {{ $orderData->payment_method }}
                </p>
                @php
                    $subtotal = 0;
                @endphp
                <h3 style="color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Order Summary</h3>
                <table width="100%" cellpadding="5" cellspacing="0"
                    style="border-collapse: collapse; margin: 10px 0;">
                    @foreach ($orderData->items as $item)
                        @php
                        $subtotal += (int)$item->total_price;
                        @endphp
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td>
                                <p style="margin: 0; font-size: 14px;"><strong>{{ $item->product_name }}</strong></p>
                                <p style="margin: 5px 0; color: #555;">{{ $item->product_quantity }} x
                                    {{ $item->product_price }} (Original Price: {{ $item->total_price }})</p>
                            </td>
                        </tr>
                    @endforeach
                </table>

                <table width="100%" cellpadding="5" cellspacing="0" style="margin-top: 10px;">
                    <tr>
                        <td style="text-align: left;"><strong>Subtotal:</strong></td>
                        <td style="text-align: right; color: #333;">{{ $subtotal }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;"><strong>Delivery Charge:</strong></td>
                        <td style="text-align: right; color: #333;">{{ $orderData->delivery_charge }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;"><strong>Tax Amount:</strong></td>
                        <td style="text-align: right; color: #333;">{{ $orderData->taxes }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-size: 18px; font-weight: bold;">Grand Total:</td>
                        <td style="text-align: right; color: #ffbb00; font-size: 18px; font-weight: bold;">
                            {{ $orderData->total_bill }}</td>
                    </tr>
                </table>

                <p style="margin-top: 20px; color: #555;">We hope you enjoy your meal!</p>
            </td>
        </tr>
        <tr>
            <td
                style="background-color: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #777; border-radius: 0 0 10px 10px;">
                &copy; 2024 Crust House | All Rights Reserved
            </td>
        </tr>
    </table>
</body>

</html>
