<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Web | Forgot Password</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
    <div class="login-logo">
        <b>Security Web</b>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
        <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

        <form action="/forgot_password" method="post">
        @csrf
        <div class="input-group mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @error('email')
        <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Request new password</button>
            </div>
            <!-- /.col -->
        </div>
        </form>

        @if($errors->any())
            <div class="alert alert-danger fade show mt-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success fade show mt-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div>{{ session('success') }}</div>
            {{-- @if(session('reset_token'))
                <hr class="my-2">
                <small>
                    <strong>Link Reset Password untuk Testing:</strong><br>
                    <a href="{{ url('/reset_password/' . session('reset_token')) }}" target="_blank" class="alert-link">
                        {{ url('/reset_password/' . session('reset_token')) }}
                    </a>
                </small>
            @endif --}}
            </div>
        </div>
        @endif
        @if(session('failed'))
            <div class="alert alert-danger fade show mt-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('failed') }}
            </div>
        @endif

        <p class="mt-2 mb-1">
            <a href="/login">Login</a>
        </p>
        <p class="mb-0">
            <a href="/register" class="text-center">Register a new membership</a>
        </p>

    </div>
        <!-- /.login-card-body -->
    </div>
    <!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
