<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .price-tag {
            font-size: 2rem;
            font-weight: bold;
            color: #495057;
        }
        .btn-subscribe {
            background-color: #007bff;
            color: #fff;
        }
        .btn-subscribe:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Choose Your Subscription Plan</h2>

    <div class="row g-4">
        @foreach ($features as $feature)
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="card-title text-center">{{ $feature->name }}</h5>
                    <p class="text-muted text-center">{{ $feature->description }}</p>
                    <div class="price-tag text-center">₹{{ number_format($feature->price, 2) }}</div>

                    <ul class="list-group list-group-flush mt-3">
                        @foreach ($feature->details as $detail)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $detail->title }}</span>
                                <span class="badge bg-primary">₹{{ number_format($detail->price, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="text-center mt-4">
                        {{-- <a href="{{ route('subscription.payment', ['id' => $feature->id]) }}" class="btn btn-subscribe w-100">Subscribe Now</a> --}}
                        <a href="{{ route('initiate.payment', ['feature_id' => $feature->id]) }}" class="btn btn-subscribe w-100">Subscribe Now</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
