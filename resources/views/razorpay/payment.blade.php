<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    var options = {
        "key": "{{ $data['key'] }}",
        "amount": "{{ $data['order_amount_paise'] }}",
        "currency": "INR",
        "name": "{{ $data['name'] }}",
        "description": "Payment for service",
        "order_id": "{{ $data['order_id'] }}",
        "handler": function (response) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ url('/dashboard/razorpay/success') }}";

            let token = document.createElement('input');
            token.name = '_token';
            token.value = "{{ csrf_token() }}";
            form.appendChild(token);

            let paymentId = document.createElement('input');
            paymentId.name = 'razorpay_payment_id';
            paymentId.value = response.razorpay_payment_id;
            form.appendChild(paymentId);

            let orderId = document.createElement('input');
            orderId.name = 'order_id';
            orderId.value = "{{ $data['order_id'] }}";
            form.appendChild(orderId);

            let amount = document.createElement('input');
            amount.name = 'amount';
            amount.value = "{{ $data['amount'] }}";
            form.appendChild(amount);

            document.body.appendChild(form);
            form.submit();
        },
        "prefill": {
            "name": "{{ $data['name'] }}",
            "email": "{{ $data['email'] }}",
            "contact": "{{ $data['contact'] }}"
        },
        "theme": {
            "color": "#528FF0"
        }
    };

    var rzp = new Razorpay(options);
rzp.open();
</script>