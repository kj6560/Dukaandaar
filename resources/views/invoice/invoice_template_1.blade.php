
----------------------------
    *** INVOICE ***       
----------------------------
Order ID   : {{ $order->order_id }}
Date       : {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y H:i') }}
----------------------------
Org        : {{ $organization->org_name ?? 'Customer' }}
Customer   : {{ $order->customer_name ?? 'Customer' }}
----------------------------

@foreach ($orderDetails as $item)
{{ Str::limit($item['product_name'], 20) }} x{{ $item['quantity'] }}
Price      : {{ $currency }} {{ number_format($item['base_price'], 2) }}
Discount   : {{ $currency }} {{ number_format($item['discount'], 2) }}
Tax        : {{ $currency }} {{ number_format($item['tax'], 2) }}
Net        : {{ $currency }} {{ number_format($item['net_price'], 2) }}
----------------------------
@endforeach
Order Total: {{ $currency }} {{ number_format($order->total_order_value, 2) }}
Discount   : {{ $currency }} {{ number_format($order->total_order_discount, 2) }}
Tax        : {{ $currency }} {{ number_format($order->tax, 2) }}
Net Total  : {{ $currency }} {{ number_format($order->net_total, 2) }}

----------------------------
Thank you for your purchase!
----------------------------

----------------------------