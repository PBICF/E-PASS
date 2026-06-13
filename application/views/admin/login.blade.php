<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pass Management</title>
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.2);
            border: none;
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            text-align: center;
            padding-top: 2rem;
        }
        .btn-login {
            background: #667eea;
            border: none;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
        }
        .icon-inside {
            position: relative;
        }
        .icon-inside i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .icon-inside .form-control {
            padding-left: 2.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-2 fw-bold">Welcome Back</h3>
                    <p class="text-muted">Sign in to manage pass types</p>
                </div>
                <div class="card-body p-4 pt-0">

                    {{-- CI3 flash messages (success/error) --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- CI3 validation errors (if using form_validation library) --}}
                    @if (isset($validation_errors) && !empty($validation_errors))
                        <div class="alert alert-danger">
                            {{ $validation_errors }}
                        </div>
                    @endif

                    <form method="POST" action="{{ site_url('admin/process_login') }}">
                        <div class="mb-3 icon-inside">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" 
                                   placeholder="Username" value="{{ set_value('username') }}" required autofocus>
                        </div>

                        <div class="mb-3 icon-inside">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Password" required>
                        </div>

                        <button type="submit" class="btn btn-login w-100 py-2 text-white fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>