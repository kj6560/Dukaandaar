<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $order->order_id }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 40px;
            background-color: #f9f9f9;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h2 {
            margin-top: 0;
            color: #4A90E2;
        }

        .invoice-header {
            margin-bottom: 20px;
        }

        .invoice-header p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background: #f0f4f8;
            color: #333;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }

        tfoot td {
            font-weight: bold;
            background: #fafafa;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="invoice-header">
            <h2>Invoice #{{ $order->order_id }}</h2>
            <p><strong>Organization:</strong> {{ $org->org_name }}</p>
            <p><strong>Org Number:</strong> {{ $org->org_number }}</p>
            <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
        </div>

        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Base Price</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Net Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach (json_decode($order->order_details, true) as $item)
                <tr>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>₹{{ number_format($item['base_price'], 2) }}</td>
                    <td>₹{{ number_format($item['discount'], 2) }}</td>
                    <td>₹{{ number_format($item['tax'], 2) }}</td>
                    <td>₹{{ number_format($item['net_price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Order Value</td>
                    <td>₹{{ number_format($order->total_order_value, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Discount</td>
                    <td>₹{{ number_format($order->total_order_discount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Tax</td>
                    <td>₹{{ number_format($order->tax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;">Net Total</td>
                    <td>₹{{ number_format($order->net_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <p style="margin-top: 30px; text-align: center;">Thank you for your purchase!</p>
    </div>
</body>

</html>