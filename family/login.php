<?php
session_start();

// Hardcoded credentials
$valid_username = 'admin';
$valid_password = 'password123';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['family_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Family Management</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 1rem; border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); }
        .btn-gradient { background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%); color: white; border: none; }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Welcome Back</h4>
            <p class="text-muted small">Please login to continue</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-gradient w-100 py-2 fw-bold mt-2">Login</button>
        </form>
    </div>
</body>
</html>
