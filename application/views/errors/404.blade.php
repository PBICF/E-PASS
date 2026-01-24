<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ @asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ @asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ @asset('assets/favicon/favicon-16x16.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/bootstrap.min.css') }}">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        .error-box {
            background: #fff;
            border-radius: 1rem;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #dc3545;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">
                <div class="error-box">

                    <div class="error-code">404</div>
                    <h2 class="mb-3">Page Not Found</h2>

                    <p class="text-muted mb-4">
                        The page you are looking for might have been removed,
                        had its name changed, or is temporarily unavailable.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= base_url(); ?>" class="btn btn-primary">
                            Go to Homepage
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            Go Back
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
