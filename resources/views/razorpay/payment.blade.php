<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<h1>Pay with Razorpay</h1>

<form id="paymentForm" action="/create-order" method="POST">
    @csrf
    <input type="number" name="amount" placeholder="Enter amount" required>
    <select name="currency">
        <option value="INR">INR</option>
        <option value="USD">USD</option>
    </select>
    <button type="submit">Pay Now</button>
</form>

<script>
    document.getElementById('paymentForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const response = await fetch('/create-order', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        const options = {
            key: data.key,
            amount: data.amount,
            currency: data.currency,
            order_id: data.order_id,
            name: "Your Company",
            description: "Payment for Order #" + data.order_id,
            handler: function (response) {
                console.log("Payment Successful: ", response);
                alert("Payment Successful: " + response.razorpay_payment_id);
            },
            prefill: {
                name: "John Doe",
                email: "john@example.com",
                contact: "+919876543210"
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    });
</script>

</body>
</html>
