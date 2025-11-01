<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9fafb;
        }
        .order-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .item {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;
        }
        .item:last-child {
            border-bottom: none;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #2563eb;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
    </div>

    <div class="content">
        <p>Dear {{ $order->user->name }},</p>
        <p>Thank you for your order! Your payment has been received and your order is being processed.</p>

        <div class="order-details">
            <h2>Order Details</h2>
            <p><strong>Order Reference:</strong> {{ $order->order_reference }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>

            <h3 style="margin-top: 20px;">Items Ordered:</h3>
            @foreach($order->items as $item)
                <div class="item">
                    <strong>{{ $item->product_name }}</strong> ({{ ucfirst($item->product_type) }})<br>
                    Quantity: {{ $item->quantity }} × ₦{{ number_format($item->price, 2) }} = ₦{{ number_format($item->subtotal, 2) }}

                    @if($item->product_type === 'digital' && $item->download)
                        <br><br>
                        <a href="{{ route('download', $item->download->download_token) }}" style="color: #2563eb;">
                            Download Now
                        </a>
                        <br>
                        <small style="color: #6b7280;">
                            Downloads: {{ $item->download_limit ?? 'Unlimited' }} |
                            Expires: {{ $item->download->expires_at ? $item->download->expires_at->format('d M, Y') : 'Never' }}
                        </small>
                    @endif
                </div>
            @endforeach

            <div class="total">
                <p>Subtotal: ₦{{ number_format($order->subtotal, 2) }}</p>
                <p>Delivery Fee: ₦{{ number_format($order->delivery_fee, 2) }}</p>
                <p style="font-size: 20px; color: #2563eb;">Total Paid: ₦{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        @if($order->delivery_option)
            <div class="order-details">
                <h3>Delivery Information</h3>
                <p>
                    {{ $order->delivery_full_name }}<br>
                    {{ $order->delivery_phone }}<br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->delivery_city }}, {{ $order->delivery_lga }}<br>
                    {{ $order->delivery_state }}
                </p>
                <p><strong>Delivery Option:</strong> {{ ucfirst($order->delivery_option) }}</p>
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('orders.show', $order->id) }}" class="button">View Order Details</a>
        </div>

        <p>If you have any questions about your order, please don't hesitate to contact us.</p>
        <p>Thank you for shopping with us!</p>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }}</p>
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
