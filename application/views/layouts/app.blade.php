<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'App')</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ @asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ @asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ @asset('assets/favicon/favicon-16x16.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/sweetalert.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ @asset('assets/css/styles.css') }}" />
    @yield('styles')
    <script defer src="{{ @asset('assets/js/mask.min.js') }}" type="text/javascript"></script>
    <script defer src="{{ @asset('assets/js/alpinejs.min.js') }}" type="text/javascript"></script>
    <script src="{{ @asset('assets/js/jquery-3.3.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ @asset('assets/js/jquery-ui.js') }}" type="text/javascript"></script>
    <script src="{{ @asset('assets/js/sweetalert.min.js') }}" type="text/javascript"></script>
    <script src="{{ @asset('assets/js/app.js') }}?version={{ time() }}" type="text/javascript"></script>
    @yield('scripts')
</head>

<body 
    class="d-flex flex-column min-vh-100"
    x-data="App()" 
    x-init="init()" 
    :data-theme="theme"
>
    
    <div class="bg-gradient mb-4 w-100 d-flex flex-column mb-4">
        <nav class="navbar">
            <div class="container-md">
                <a class="navbar-brand" href="{{ site_url('/') }}">
                    <div class="flex items-center justify-center gap-3">
                        <img src="{{ @asset('assets/images/mr_logo.png') }}" alt="ICF" height="45">
                        <h2 class="title text-lg font-semibold">@yield('title', 'App')</h2>
                    </div>
                </a>
                <button
                    class="theme-toggle d-none"
                    x-on:click="toggleTheme()"
                    :class="{ 'is-dark': theme === 'dark' }"
                    aria-label="Toggle theme"
                >
                    <span class="knob"></span>
                </button>
            </div>
        </nav>
    </div>

    @if($error = flashdata('error', null))
        <div class="container-md">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">{!! $error !!}</div>
                </div>
            </div>
        </div>
    @endif
    @if($success = flashdata('success', null))
        <div class="container-md">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">{{ $success }}</div>
                </div>
            </div>
        </div>
    @endif

    <main class="container-md flex-grow-1">
        @yield('content')
    </main>
    <footer class="py-3 my-4 text-muted text-center">
        &copy; {{ date('Y') }} Designed and developed by Personnel Branch, <a href="https://pb.icf.gov.in/">ICF</a>
    </footer>
</body>
</html>
