<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.3/css/all.min.css" rel="stylesheet">
    <title>FizSell Mobile POS</title>
    <style>
        body, html {
            height: 100%;
        }
        .bg-image {
            background-image: url('https://source.unsplash.com/1600x900/?business,technology');
            background-size: cover;
            background-position: center;
            height: 100%;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .login-btn {
            background-color: #007bff;
            color: #fff;
        }
        .login-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="bg-image d-flex justify-content-center align-items-center">
    <div class="login-container col-md-4">
        <h3 class="text-center mb-4">FizSell</h3>
        <form action="/loginRequest" method="post">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email"  name="email" class="form-control" id="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <input type="checkbox" id="rememberMe">
                    <label for="rememberMe">Remember Me</label>
                </div>
                <a href="#" class="text-primary">Forgot Password?</a>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn login-btn">Login</button>
            </div>

            <div class="text-center mt-3">
                <p class="mb-0">Don't have an account? <a href="/register" class="text-primary">Register</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
