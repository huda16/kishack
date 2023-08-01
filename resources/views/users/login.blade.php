<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="shortcut icon" href="{{ url('img/logo.svg') }}" type="image/x-icon">
    <!-- Google Font: Ubuntu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ url('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('dist/css/adminlte.min.css') }}">
    <!-- Custom style -->
    <link rel="stylesheet" href="{{ url('css/users.css') }}">
</head>

<body class="bg-image d-flex flex-column min-vh-100 justify-content-center align-items-center">
    <main class="container">
        <div class="w-50 bg-white mx-auto pt-3 rounded-lg text-center">
            <div class="text-center">
                <a href="/" class="h1">
                    <img src="{{ url('img/logo.svg') }}" alt="Logo">
                </a>
            </div>

            <h1 class="h3 font-weight-bold mt-4 text-black-50">Sign In</h1>
            <p class="login-box-msg mb-4">Enter your email and password to log in</p>

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('loginError'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('loginError') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mx-4">
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" name="email">
                        <span class="error invalid-feedback text-left">
                            The provided credentials do not match our records.
                        </span>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password">
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="icheck-primary text-right">
                                <a href="/forgot-password">Forgot Password ?</a>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-5 py-2 my-5 mb-5">Login</button>
                </form>
            </div>

        </div>
    </main>

    <footer class="text-center text-white mt-5">v0.0.1 © 2022</footer>

    <!-- jQuery -->
    <script src="{{ url('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ url('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('dist/js/adminlte.min.js') }}"></script>
</body>

</html>
