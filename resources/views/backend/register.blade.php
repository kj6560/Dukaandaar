<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5 0%, #9face6 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #6c63ff;
    }
    .btn-primary {
      background-color: #6c63ff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #5848c2;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card p-4">
          <div class="card-body">
            <h3 class="text-center mb-4">Create an Organization</h3>

            <!-- Display success message -->
            @if (session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
            @endif

            <!-- Display error message -->
            @if (session('error'))
              <div class="alert alert-danger">
                {{ session('error') }}
              </div>
            @endif

            <!-- Display validation errors -->
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="/registerOrg" method="POST">
              @csrf
              <div class="mb-3">
                <label for="org_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="org_name" id="org_name" value="{{ old('org_name') }}" placeholder="Enter Organization name" required>
              </div>
              <div class="mb-3">
                <label for="org_email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="org_email" id="org_email" value="{{ old('org_email') }}" placeholder="Enter Organization email address" required>
              </div>
              <div class="mb-3">
                <label for="org_number" class="form-label">Number</label>
                <input type="text" class="form-control" name="org_number" id="org_number" value="{{ old('org_number') }}" placeholder="Enter Number" required>
              </div>
              <div class="mb-3">
                <label for="org_address" class="form-label">Address</label>
                <input type="text" class="form-control" name="org_address" id="org_address" value="{{ old('org_address') }}" placeholder="Enter Org Address" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register Organization</button>
              </div>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="/login">Login here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
